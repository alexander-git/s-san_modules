var StationController = (function() {
    
    
    var Constr = function(params) {
        this._sel = new StationSelectors();
        this._backend = new StationBackend(params);
        this._dataProcessor = new StationDataProcessor(
            params.orderStageIds,
            params.orderItemLogStates
        );

        this._timeOffset = params.timeOffset;

        this._cityId = parseInt(params.cityId);
        this._stationId = parseInt(params.stationId);
        this._stationOrdersCount = parseInt(params.stationOrdersCount);
        
        this._cardTemplate = null;
    
        this._headerDefaultTemplate = null;
        this._headerInWorkTemplate = null;
        this._headerCanceledTemplate = null;

        this._productNewTemplate = null;
        this._productNewAtInWorkCardTemplate = null;
        this._productPreparingTemplate = null;
        this._productPreparedTemplate = null;

        this._infoDefaultTemplate = null;
        this._infoCanceledTemplate = null;
        this._infoPreparingTemplate = null;
        this._infoPreparedTemplate = null;
        
        this._defaultCardClasses = params.defaultCardClasses;
        this._inWorkCardClasses = params.inWorkCardClasses;
        this._canceledCardClasses = params.canceledCardClasses;
        
        this._cards = [];
        this._requestsCount = 0;
    };
    
    Constr.prototype.init = function() {
        this._initCurrentTime();
        this._prepareTemplates();
        this._prepareCards();
        this._initCardEvents();
        this._getCards();
        this._initCardsUpdate();
    };
    
    Constr.prototype._initCurrentTime = function() {
        var that = this;
        
        setInterval(function() {
            that._updateCurrentTime();
        }, 1000);
        
       that._updateCurrentTime();
    };
    
          
    Constr.prototype._prepareTemplates = function() {
        this._cardTemplate = $(this._sel.cardTemplate).html();
        this._headerDefaultTemplate = $(this._sel.headerDefaultTemplate).html();
        this._headerInWorkTemplate = $(this._sel.headerInWorkTemplate).html();
        this._headerCanceledTemplate = $(this._sel.headerCanceledTemplate).html();

        this._productNewTemplate = $(this._sel.productNewTemplate).html();
        this._productPreparingTemplate = $(this._sel.productPreparingTemplate).html();
        this._productPreparedTemplate = $(this._sel.productPreparedTemplate).html();

        this._infoDefaultTemplate = $(this._sel.infoDefaultTemplate).html();
        this._infoCanceledTemplate = $(this._sel.infoCanceledTemplate).html();
        this._infoPreparingTemplate = $(this._sel.infoPreparingTemplate).html();
        this._infoPreparedTemplate = $(this._sel.infoPreparedTemplate).html();

        Mustache.parse(this._headerDefaultTemplate);
        Mustache.parse(this._headerInWorkTemplate);
        Mustache.parse(this._headerCanceledTemplate);
        
        Mustache.parse(this._productNewTemplate);
        Mustache.parse(this._productPreparingTemplate);
        Mustache.parse(this._productPreparedTemplate);
        
        Mustache.parse(this._infoDefaultTemplate);
        Mustache.parse(this._infoCanceledTemplate);
        Mustache.parse(this._infoPreparingTemplate);
        Mustache.parse(this._infoPreparedTemplate);
    };
    
    Constr.prototype._prepareCards = function() {
        for (var i = 0; i < this._stationOrdersCount; i++) {
            this._cards.push({
                'order' : null,
                'intervalHandler' : null
            });
        }
    };
            
    Constr.prototype._initCardEvents = function() {
        var that = this;
        
        $(document).on('click', this._sel.cardInWorkButton, function(e) {
            that._cardInWorkButtonClick(this, e);
        });
        
        $(document).on('click', this._sel.cardCancelButton, function(e) {
            that._cardCancelButtonClick(this, e);
        });
    
        $(document).on('click', this._sel.cardCompleteButton, function(e) {
            that._cardCompleteButtonClick(this, e);
        });
    
        $(document).on('click', this._sel.cardProduct, function(e) {
            that._cardProductClick(this, e);
        });
    
    };
    
    Constr.prototype._initCardsUpdate = function() {
        var that = this;
        var upadateInterval = 20 * 1000;
        setInterval(function() {
            if (that._requestsCount >  0) {
                return;
            }
            that._updateCards();
        }, upadateInterval);
    };
    
    Constr.prototype._getCards = function() {
        var that = this;
        ++this._requestsCount;
        this._backend.getCards(this._cityId, this._stationId,
            function(data) {

                that._getCardsSuccess(data);
                --that._requestsCount;
            },
            function() {
                
                that._showDefaultErrorMessage();
                --that._requestsCount;
            }
        );
    };
    
    Constr.prototype._updateCurrentTime = function() {
        var time = new Date();
        time.setTime(time.getTime() + (this._timeOffset * 1000));
        var hours = time.getUTCHours();
        var minutes = time.getUTCMinutes();
        var hoursStr;
        if (hours < 10) {
            hoursStr = '0'+hours;
        } else {
            hoursStr = hours;
        }
        var minutesStr;
        if (minutes < 10) {
            minutesStr = '0'+minutes; 
        } else {
            minutesStr = minutes;
        }
        $(this._sel.currentTime).text(hoursStr+':'+minutesStr);
    };
            
    Constr.prototype._redrawCards = function() {
        var that = this;
        
        $(this._sel.cardsContainer).html('');
        
        for (var i = 0; i < this._cards.length; i++) {
            var order = this._cards[i].order;
            if (order === null) {
                continue;
            }
            this._drawCard(order);
        }
        
        for (var i = 0; i < this._cards.length; i++) {
            var order = this._cards[i].order;
            if (order === null) {
                continue;
            }
            var intervalHandler = this._cards[i].intervalHandler;
            if (intervalHandler !== null) {
                clearInterval(intervalHandler);
                this._cards[i].intervalHandler = null;
            }
            
            if (this._dataProcessor.isOrderOnStationInWork(order, this._stationId)) {
                that._updateWorkTimeInCardInfo(i);
                that._setUpdateWorkTimeInCardInfo(i);
            }        
        }
    };
    
    Constr.prototype._setUpdateWorkTimeInCardInfo = function(cardIndex) {
        var that = this;
        this._cards[cardIndex].intervalHandler = setInterval(function() {
              that._updateWorkTimeInCardInfo(cardIndex);
         }, 1000);
    };
    
    Constr.prototype._updateWorkTimeInCardInfo = function(cardIndex) {
        var order = this._cards[cardIndex].order;
        var dateStart = this._dataProcessor.getProductsDateStart(order, this._stationId);
        var utcTime = Math.floor(this._getCurrentUTCTime() / 1000);
        var cardSelector = $(this._sel.getCardSelector(order.id));
        var timeExpired = utcTime - dateStart;
        
        var hours = Math.floor(timeExpired/3600);
        var minutes = Math.floor((timeExpired - hours * 3600)/60);
        var seconds = timeExpired % 60;
        var hoursStr = '';
        var minutesStr = '';
        var secondsStr = '';
        if (hours !== 0) {
            if (hours < 10) {
                 hoursStr = '0'+hours;
            } else {
                hoursStr = hours;
            }
        }

        if (minutes < 10) {
            minutesStr = '0'+minutes;
        } else {
            minutesStr = minutes;
        }
        if (seconds < 10) {
            secondsStr = '0'+seconds;
        } else {
            secondsStr = seconds;
        }
        
        var timeStr = '';
        if (hoursStr !== '') {
            timeStr += hoursStr+':';
        } 
        timeStr += minutesStr+':'+secondsStr;
        
        $(cardSelector).find(this._sel.cardInfoTimer).html(timeStr);
    };
    
    Constr.prototype._getCurrentUTCTime = function() {
        return (new Date()).getTime();
    };
    
    Constr.prototype._drawCard = function(order) {
        var orderId = order.id;
        var cardHtml = Mustache.render(this._cardTemplate, {
            'orderId' : orderId
        });
        
        $(cardHtml).appendTo(this._sel.cardsContainer);  
        // Добавим нужный css класс в зависимости от стадии заказа.
        var cardTagSelector = this._sel.getCardSelector(orderId);
        var rootCardTag = $(cardTagSelector);
        if (this._dataProcessor.isOrderDefault(order, this._stationId)) {
            this._addCssClassesToTag(rootCardTag, this._defaultCardClasses);
        } else if (this._dataProcessor.isOrderOnStationInWork(order, this._stationId)) {
            this._addCssClassesToTag(rootCardTag, this._inWorkCardClasses);
        }else if (this._dataProcessor.isOrderCanceled(order)) {
            this._addCssClassesToTag(rootCardTag, this._canceledCardClasses);
        } 
                
        // Создадим html заголовка.
        var headerHtml = this._getCardHeaderHtml(order);
        var cardHeaderContainer = $(cardTagSelector).find(this._sel.cardHeaderContainer);
        cardHeaderContainer.html(headerHtml);
    
        // Создадим html продуктов.
        var productsHtml = '';
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            var orderItemLog = order.orderItemLogs[i];
            productsHtml += this._getCardProductHtml(order, orderItemLog);
        }
        
        var cardBodyContainer = $(cardTagSelector).find(this._sel.cardBodyContainer);
        cardBodyContainer.html(productsHtml);

        
        var infoHtml = this._getInfoHtml(order);
        var cardInfoContainr = $(cardTagSelector).find(this._sel.cardInfoContainer);
        cardInfoContainr.html(infoHtml);

        return cardHtml;
    };
    
    Constr.prototype._getCardHeaderHtml = function(order) {
        var headerHtml = '';
        var productsStationNumber = this._dataProcessor.getProductsStationNumber(order, this._stationId);
        if (this._dataProcessor.isOrderDefault(order, this._stationId)) {
            headerHtml = Mustache.render(this._headerDefaultTemplate, {
                'time' : order.delivery_time,
                'date' : order.delivery_date
            });
        } else if (this._dataProcessor.isOrderOnStationInWork(order, this._stationId)) {
            headerHtml = Mustache.render(this._headerInWorkTemplate, {
                'time' : order.delivery_time,
                'date' : order.delivery_date,
                'number' : productsStationNumber
            });
        } else if (this._dataProcessor.isOrderCanceled(order)) {
            var hasNumber = productsStationNumber !== null;
             headerHtml = Mustache.render(this._headerCanceledTemplate, {
                'time' : order.delivery_time,
                'date' : order.delivery_date,
                'hasNumber' : hasNumber,
                'number' : productsStationNumber,
            });
        }
        
        return headerHtml;
    };
    
    Constr.prototype._getCardProductHtml = function(order, orderItemLog) {
        var productHtml = '';
        var renderParams = {
            'name' : orderItemLog.productName,
            'quantity' : orderItemLog.quantity,
            'productId' : orderItemLog.product_id,
            'orderId' : orderItemLog.order_id,
        };
        
        if (this._dataProcessor.isOrderItemLogNew(orderItemLog)) {
            productHtml = Mustache.render(this._productNewTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogPreparing(orderItemLog)) {
            productHtml = Mustache.render(this._productPreparingTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogPrepared(orderItemLog)) {
            productHtml = Mustache.render(this._productPreparedTemplate, renderParams);
        } else {
            throw new Error('Неверное состояние продукта');
        }
        
        return productHtml;
    };
    
    Constr.prototype._getInfoHtml = function(order) {
        var infoHtml = '';
        
        if (this._dataProcessor.isOrderDefault(order, this._stationId)) {
            infoHtml = Mustache.render(this._infoDefaultTemplate);
        } else if (this._dataProcessor.isOrderOnStationInWork(order, this._stationId)) {
            if (this._dataProcessor.areAllProductsPrepared(order)) {
                infoHtml = Mustache.render(this._infoPreparedTemplate);
            } else {
                infoHtml = Mustache.render(this._infoPreparingTemplate);
            }
        } else if (this._dataProcessor.isOrderCanceled(order)) {
            infoHtml = Mustache.render(this._infoCanceledTemplate);
        }
        
        return infoHtml;
    };
    
    Constr.prototype._addCssClassesToTag = function(tag, classes) {
        var tagSelection = $(tag);
                
        if (classes instanceof Array) {
            for (var i = 0; i < classes.length; i++) {

                tagSelection.addClass(classes[i]);
            }
        } else {
            tagSelection.addClass(classes);
        }  
    };
    
    Constr.prototype._cardInWorkButtonClick = function(button, event) {
        var that = this;
        button = $(button);
        var card = button.closest(this._sel.card);
        var orderId = this._getOrderIdOnCard(card);
        
        ++this._requestsCount;
        this._backend.startCardInWorkUrl(orderId, this._stationId, 
            function(data) {
                that._startCardInWorkSuccess(data);
                --that._requestsCount;
            },
            function() {
                that._showDefaultErrorMessage();
                --that._requestsCount;
            }
        );
    };
    
    Constr.prototype._cardCompleteButtonClick = function(button, event) {
        var that = this;
        button = $(button);
        var card = button.closest(this._sel.card);
        var orderId = this._getOrderIdOnCard(card);
        
        ++this._requestsCount;
        this._backend.completeCard(orderId, this._stationId, 
            function(data) {
                that._cardCompleteSuccess(data);
                --that._requestsCount;
                
            },
            function() {
                that._showDefaultErrorMessage();
                --that._requestsCount;
            }
        
        );
    };
    
    Constr.prototype._cardCancelButtonClick = function(button, event) {
        var that = this;
        button = $(button);
        var card = button.closest(this._sel.card);
        var orderId = this._getOrderIdOnCard(card);
        
        ++this._requestsCount;
        this._backend.cancelCard(orderId, this._stationId, 
            function(data) {
                that._cardCancelSuccess(data);
                --that._requestsCount;
                
            },
            function() {
                that._showDefaultErrorMessage();
                --that._requestsCount;
            }
        
        );
    };
    
    
    Constr.prototype._cardProductClick = function(product, event) {
        var that = this;
        product = $(product);
        var card = product.closest(this._sel.card);
        var productId = this._getProductIdOnProduct(product);
        var orderId = this._getOrderIdOnCard(card);
        var orderItemLog = this._findOrderItemLog(orderId, productId);
        
        if (
            !this._dataProcessor.isOrderItemLogPreparing(orderItemLog) &&
            !this._dataProcessor.isOrderItemLogPrepared(orderItemLog)
        ) {
            return;
        }
                
        if (this._dataProcessor.isOrderItemLogPreparing(orderItemLog)) {
            ++this._requestsCount;
            this._backend.setProductPrepared(orderId, productId, 
                function(data) {
                    that._setProductPreparedSuccess(data);
                    --that._requestsCount;
                },
                function() {
                    that._showDefaultErrorMessage();
                    --that._requestsCount;
                }
            );
            
            return;
        }
        
        if (this._dataProcessor.isOrderItemLogPrepared(orderItemLog)) {
            ++this._requestsCount;
            this._backend.setProductPreparing(orderId, productId, 
                function(data) {
                    that._setProductPreparingSuccess(data);
                    --that._requestsCount;
                },
                function() {
                    that._showDefaultErrorMessage();
                    --that._requestsCount;
                }
            );
    
            return;
        }
        
    };
    
    Constr.prototype._updateCards = function()
    {
        var that = this;
        
        var orderIds = [];
        for (var i = 0; i < this._cards.length; i++) {
            var order = this._cards[i].order;
            if (order === null) {
                continue;
            }
            if (this._dataProcessor.isOrderCanceled(order)) {
                orderIds.push(order.id);
            }
            if (this._dataProcessor.isOrderOnStationInWork(order, this._stationId)) {
                orderIds.push(order.id);
            }
        }
        
        ++this._requestsCount;
        this._backend.updateCards(this._cityId, this._stationId, orderIds, 
            function(data) {
                that._updateCardsSuccess(data);
                --that._requestsCount;
            },
            function() {
                that._showDefaultErrorMessage();
                --that._requestsCount;
            }
        );
        
    };
    
    Constr.prototype._getCardsSuccess = function(data) {
        this._updateCardsOnOrders(data);
    };
        
    Constr.prototype._updateCardsSuccess = function(data) {
        this._updateCardsOnOrders(data);
    };
    
    Constr.prototype._startCardInWorkSuccess = function(data) {
        if (typeof data.success === 'undefined' || !data.success) {
            this._showDefaultErrorMessage();
            return;
        }

        this._updateExistCard(data.order);
        this._redrawCards();
    };
    
    Constr.prototype._cardCompleteSuccess = function(data)
    {
        if (typeof data.success === 'undefined' || !data.success) {
            this._showDefaultErrorMessage();
            return;
        }
        
        var orderId = data.orderId;
        this._deleteExistCard(orderId);
        $(this._sel.getCardSelector(orderId)).remove();
        this._redrawCards();
        this._updateCards();
    };

    Constr.prototype._cardCancelSuccess = function(data) {   
        if (typeof data.success === 'undefined' || !data.success) {
            this._showDefaultErrorMessage();
            return;
        }
        
        var orderId = data.orderId;
        this._deleteExistCard(orderId);
        $(this._sel.getCardSelector(orderId)).remove(); 
        this._redrawCards();
        this._updateCards();
    };
    
    Constr.prototype._setProductPreparingSuccess = function(data) {
        if (typeof data.success === 'undefined' || !data.success) {
            this._showDefaultErrorMessage();
            return;
        }
        
        this._updateExistCard(data.order);
        this._redrawCards();
    };
    
    Constr.prototype._setProductPreparedSuccess = function(data) {
        if (typeof data.success === 'undefined' || !data.success) {
            this._showDefaultErrorMessage();
            return;
        }
        
        this._updateExistCard(data.order);
        this._redrawCards();
    };
        
    Constr.prototype._showDefaultErrorMessage = function() {
        alert('Произошла ошибка');
    };
    
    Constr.prototype._cleanCards = function() {
        for (var i = 0; i < this._stationOrdersCount; i++) {
            var card = this._cards[i];
            if (card.intervalHandler !== null) {
                clearInterval(card.intervalHandler);
            }
            this._cards[i] = {
                'order' : null,
                'intervalHandler' : null
            };
        }
    };
    
    Constr.prototype._updateExistCard = function(order) {
        for (var i = 0; i < this._stationOrdersCount; i++) {
            var card = this._cards[i];
            if (card.order === null) {
                continue;
            }
            
            if (card.order.id === order.id) {
                card.order = order;
                if (card.intervalHandler !== null) {
                    clearInterval(card.intervalHandler);
                    card.intervalHandler = null;
                }
            }
        }
    };
    
    Constr.prototype._deleteExistCard = function(orderId) {
        for (var i = 0; i < this._stationOrdersCount; i++) {
            var card = this._cards[i];
            if (card.order === null) {
                continue;
            }
            
            if (card.order.id === orderId) {
                card.order = null;
                if (card.intervalHandler !== null) {
                    clearInterval(card.intervalHandler);
                    card.intervalHandler = null;
                }
            }
        }
    };
    
    Constr.prototype._updateCardsOnOrders = function(orders)
    {
        this._cleanCards();
        for (var i = 0; i < orders.length; i++) {
            var order = orders[i];
            this._cards[i].order = order;
        }
        
        this._redrawCards();
    };
    
    Constr.prototype._findOrder = function(orderId)
    {
        for (var i = 0; i < this._cards.length; i++) {
            var order = this._cards[i].order;
            if (order === null) {
                continue;
            }
            if (order.id === orderId) {
                return order;
            }
        }
        
        return null;
    };
    
    Constr.prototype._findOrderItemLog = function(orderId, productId)
    {
        var order = this._findOrder(orderId);
        if (order === null) {
            return null;
        }
        
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            var orderItemLog = order.orderItemLogs[i];
            if (orderItemLog.product_id === productId) {
                return orderItemLog;
            }
        }
        
        return null;
    };
    
    Constr.prototype._getOrderIdOnCard = function(card) {
        return parseInt($(card).attr('data-order-id'));
    };
    
    Constr.prototype._getProductIdOnProduct = function(product) {
        return parseInt($(product).attr('data-product-id'));
    };
    
    return Constr;
    
})();