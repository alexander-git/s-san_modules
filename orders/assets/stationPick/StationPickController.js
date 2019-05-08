var StationPickController = (function() {
    
    
    var Constr = function(params) {
        this._sel = new StationPickSelectors();
        this._backend = new StationPickBackend(params);
        
        this._cityId = params.cityId;
        this._stationId = params.stationId;
        this._timeOffset = params.timeOffset;
        
        this._checksPrintUrlTemplate = params.checksPrintUrlTemplate;
        
        this._dataProcessor = new StationPickDataProcessor(
            params.orderStageIds,
            params.orderItemLogStates,
            this._stationId
        );

        this._cardTemplate = null;
        this._headerTemplate = null;

        this._productKitchenNewTemplate = null;
        this._productKitchenPreparingTemplate = null;
        this._productKitchenPreparedTemplate = null;
        this._productKitchenCompleteTemplate = null;
        this._productKitchenAddedTemplate = null;
        this._productKitchenCanceledTemplate = null;
        this._productPickNewTemplate = null;
        this._productPickPreparingTemplate = null;
        this._productPickAddedTemplate = null;

        this._pickLabelTemplate = null;
        this._personNumTemplate = null;

        this._footerTimeTemplate = null;
        this._buttonStartPickEnabledTemplate = null;
        this._buttonStartPickDisabledTemplate = null;
        this._buttonDeliverEnabledTemplate = null;
        this._buttonDeliverDisabledTemplate = null;
        this._buttonCancelEnabledTemplate = null;
        this._buttonCancelDisabledTemplate = null;

        this._defaultCardClasses = params.defaultCardClasses;
        this._inPickCardClasses = params.inPickCardClasses;
        this._canceledCardClasses = params.canceledCardClasses;
        
        this._cards = [];
        this._runningRequestsCount = 0;
    };
    
    Constr.prototype.init = function() {
        this._initCurrentTime();
        this._prepareTemplates();
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
        this._headerTemplate = $(this._sel.headerTemplate).html();

        this._productKitchenNewTemplate = $(this._sel.productKitchenNewTemplate).html();
        this._productKitchenPreparingTemplate = $(this._sel.productKitchenPreparingTemplate).html();
        this._productKitchenPreparedTemplate = $(this._sel.productKitchenPreparedTemplate).html();
        this._productKitchenCompleteTemplate = $(this._sel.productKitchenCompleteTemplate).html();
        this._productKitchenAddedTemplate = $(this._sel.productKitchenAddedTemplate).html();
        this._productKitchenCanceledTemplate = $(this._sel.productKitchenCanceledTemplate).html();
        this._productPickNewTemplate = $(this._sel.productPickNewTemplate).html();
        this._productPickPreparingTemplate = $(this._sel.productPickPreparingTemplate).html();
        this._productPickAddedTemplate = $(this._sel.productPickAddedTemplate).html();

        this._pickLabelTemplate = $(this._sel.pickLabelTemplate).html();
        this._personNumTemplate = $(this._sel.personNumTemplate).html();

        this._footerTimeTemplate = $(this._sel.footerTimeTemplate).html();
        this._buttonStartPickEnabledTemplate = $(this._sel.buttonStartPickEnabledTemplate).html();
        this._buttonStartPickDisabledTemplate = $(this._sel.buttonStartPickDisabledTemplate).html();
        this._buttonDeliverEnabledTemplate = $(this._sel.buttonDeliverEnabledTemplate).html();
        this._buttonDeliverDisabledTemplate = $(this._sel.buttonDeliverDisabledTemplate).html();
        this._buttonCancelEnabledTemplate = $(this._sel.buttonCancelEnabledTemplate).html();
        this._buttonCancelDisabledTemplate = $(this._sel.buttonCancelDisabledTemplate).html();

        Mustache.parse(this._cardTemplate);
        Mustache.parse(this._headerTemplate);

        Mustache.parse(this._productKitchenNewTemplate);
        Mustache.parse(this._productKitchenPreparingTemplate);
        Mustache.parse(this._productKitchenPreparedTemplate);
        Mustache.parse(this._productKitchenCompleteTemplate);
        Mustache.parse(this._productKitchenAddedTemplate);
        Mustache.parse(this._productKitchenCanceledTemplate);
        Mustache.parse(this._productPickNewTemplate);
        Mustache.parse(this._productPickPreparingTemplate);
        Mustache.parse(this._productPickAddedTemplate);

        Mustache.parse(this._pickLabelTemplate);
        Mustache.parse(this._personNumTemplate);

        Mustache.parse(this._footerTimeTemplate);
        Mustache.parse(this._buttonStartPickEnabledTemplate);
        Mustache.parse(this._buttonStartPickDisabledTemplate);
        Mustache.parse(this._buttonDeliverEnabledTemplate);
        Mustache.parse(this._buttonDeliverDisabledTemplate);
        Mustache.parse(this._buttonCancelEnabledTemplate);
        Mustache.parse(this._buttonCancelDisabledTemplate);
    };
            
    Constr.prototype._initCardEvents = function() {
        var that = this;
        
        $(document).on('click', this._sel.cardStartPickButton, function(e) {
            that._cardStartPickButtonClick(this, e);
        });

        $(document).on('click', this._sel.cardDeliverButton, function(e) {
            that._cardDeliverButtonClick(this, e);
        });
        
        $(document).on('click', this._sel.cardCancelButton, function(e) {
            that._cardCancelButtonClick(this, e);
        });
    
        $(document).on('click', this._sel.cardProduct, function(e) {
            that._cardProductClick(this, e);
        });
    
    };
    
    Constr.prototype._initCardsUpdate = function() {
        var that = this;
        var upadateInterval = 20 * 1000;
        setInterval(function() {
            if (that._runningRequestsCount >  0) {
                return;
            }
            that._updateCards();
        }, upadateInterval);
    };
    
    Constr.prototype._getCards = function() {
        var that = this;
        ++this._runningRequestsCount;
        this._backend.getCards(this._cityId,
            function(data) {
                that._getCardsSuccess(data);
                --that._runningRequestsCount;
            },
            function() {
                that._showDefaultErrorMessage();
                --that._runningRequestsCount;
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
            
            if (this._dataProcessor.isOrderInWork(order)) {
                that._updateWorkTimeInCardFooter(i);
                that._setUpdateWorkTimeInCardFooter(i);
            } 
            
        }
    };
    
    Constr.prototype._setUpdateWorkTimeInCardFooter = function(cardIndex) {
        var that = this;
        this._cards[cardIndex].intervalHandler = setInterval(function() {
              that._updateWorkTimeInCardFooter(cardIndex);
         }, 1000);
    };
    
    Constr.prototype._updateWorkTimeInCardFooter = function(cardIndex) {
        var order = this._cards[cardIndex].order;
        var dateInWorkStart = this._dataProcessor.getOrderDateInWorkStart(order);
        var utcTime = Math.floor(this._getCurrentUTCTime() / 1000);
        var cardSelector = $(this._sel.getCardSelector(order.id));
        var timeExpired = utcTime - dateInWorkStart;
        
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
        
        $(cardSelector).find(this._sel.cardTimer).html(timeStr);
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
        
        var isOrderInPick = null;
        var isOrderDefault = this._dataProcessor.isOrderDefault(order);
        if (isOrderDefault) {
            isOrderInPick = this._dataProcessor.isOrderInPick(order);
        }
        
        if (isOrderDefault && !isOrderInPick) {
            this._addCssClassesToTag(rootCardTag, this._defaultCardClasses);
        } else if (isOrderDefault && isOrderInPick) {
            this._addCssClassesToTag(rootCardTag, this._inPickCardClasses);
        }else if (this._dataProcessor.isOrderCanceled(order)) {
            this._addCssClassesToTag(rootCardTag, this._canceledCardClasses);
        } 
                
        // Создадим html заголовка.
        var headerHtml = this._getCardHeaderHtml(order);
        $(cardTagSelector).find(this._sel.cardHeaderContainer).html(headerHtml);
        
        // Создадим html продуктов.
        var bodyHtml = '';
        var kitchenProductsHtml = this._getCardKitchenProductsHtml(order);
        if (kitchenProductsHtml !== '') { 
            bodyHtml += kitchenProductsHtml;
        }

        var pickProductsHtml = this._getCardPickProductsHtml(order);
        if (pickProductsHtml !== '') {
            bodyHtml += this._getPickLabelHtml();
            bodyHtml += pickProductsHtml;
        }
        bodyHtml += this._getPersonNumHtml(order);
                
        $(cardTagSelector).find(this._sel.cardBodyContainer).html(bodyHtml);
        
        var footerTimeHtml = this._getFooterTimeHtml();
        $(cardTagSelector).find(this._sel.cardFooterTimeContainer).html(footerTimeHtml);
        
        var footerButtonHtml = this._getFooterButtonHtml(order);
        $(cardTagSelector).find(this._sel.cardFooterButtonContainer).html(footerButtonHtml);
        
        return cardHtml;
    };
    
    Constr.prototype._getCardHeaderHtml = function(order) {
        var headerHtml = Mustache.render(this._headerTemplate, {
            'time' : order.delivery_time,
            'date' : order.delivery_date,
            'orderNum' : order.order_num,
        });
        
        return headerHtml;
    };
    
    Constr.prototype._getCardKitchenProductsHtml = function(order) {
        var html = '';
        var kitchenOrderItemLogs = this._dataProcessor.getKitchenOrderItemLogs(order);
 
        for (var i = 0; i < kitchenOrderItemLogs.length; i++) {
            html += this._getCardKitchenProductHtml(order, kitchenOrderItemLogs[i]);
        }
        
        return html;
    };
    
    Constr.prototype._getCardKitchenProductHtml = function(order, orderItemLog) {
        var productHtml = '';
        var renderParams = {
            'name' : orderItemLog.productName,
            'quantity' : orderItemLog.quantity,
            'productId' : orderItemLog.product_id,
            'orderId' : orderItemLog.order_id,
            'number' : orderItemLog.number,
        };
        
        if (this._dataProcessor.isOrderItemLogNew(orderItemLog)) {
            productHtml = Mustache.render(this._productKitchenNewTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogPreparing(orderItemLog)) {
            productHtml = Mustache.render(this._productKitchenPreparingTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogPrepared(orderItemLog)) {
            productHtml = Mustache.render(this._productKitchenPreparedTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogComplete(orderItemLog)) {
            productHtml = Mustache.render(this._productKitchenCompleteTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogAdded(orderItemLog)) {
            productHtml = Mustache.render(this._productKitchenAddedTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogCanceled(orderItemLog)) {
            productHtml = Mustache.render(this._productKitchenCanceledTemplate, renderParams);
        } else {
            throw new Error('Неверное состояние продукта');
        }
                
        return productHtml;
    };
    
    Constr.prototype._getCardPickProductsHtml = function(order) {
        var html = '';
        var pickOrderItemLogs = this._dataProcessor.getPickOrderItemLogs(order);
        for (var i = 0; i < pickOrderItemLogs.length; i++) {
            html += this._getCardPickProductHtml(order, pickOrderItemLogs[i]);
        }
        
        return html;
    };
    
    Constr.prototype._getCardPickProductHtml = function(order, orderItemLog) {
        var productHtml = '';
        var renderParams = {
            'name' : orderItemLog.productName,
            'quantity' : orderItemLog.quantity,
            'productId' : orderItemLog.product_id,
            'orderId' : orderItemLog.order_id
        };
        
        if (this._dataProcessor.isOrderItemLogNew(orderItemLog)) {
            productHtml = Mustache.render(this._productPickNewTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogPreparing(orderItemLog)) {
            productHtml = Mustache.render(this._productPickPreparingTemplate, renderParams);
        } else if (this._dataProcessor.isOrderItemLogAdded(orderItemLog)) {
            productHtml = Mustache.render(this._productPickAddedTemplate, renderParams);
        } else {
            throw new Error('Неверное состояние продукта');
        }
        
        return productHtml;
    };
    
    Constr.prototype._getPickLabelHtml = function() {
        return Mustache.render(this._pickLabelTemplate);
    };
    
    Constr.prototype._getPersonNumHtml = function(order) {
        return Mustache.render(this._personNumTemplate, {
            'personNum' : order.person_num,
        });
    };
    
    Constr.prototype._getFooterTimeHtml = function() {
        return Mustache.render(this._footerTimeTemplate);
    };
    
    Constr.prototype._getFooterButtonHtml = function(order) {
        var buttonHtml = '';
        
        var isOrderDefault = this._dataProcessor.isOrderDefault(order);
        var isOrderInPick = null;
        if (isOrderDefault) {
            isOrderInPick = this._dataProcessor.isOrderInPick(order);
        }
        
        if (isOrderDefault && !isOrderInPick) {
            if (this._dataProcessor.isOrderCanBeStartPicking(order)) {
                buttonHtml = Mustache.render(this._buttonStartPickEnabledTemplate); 
            } else {
                buttonHtml = Mustache.render(this._buttonStartPickDisabledTemplate);
            }
        } 
        
        if (isOrderDefault && isOrderInPick) {
            if (this._dataProcessor.isOrderCanBeDelivered(order)) {
                buttonHtml = Mustache.render(this._buttonDeliverEnabledTemplate); 
            } else {
                buttonHtml = Mustache.render(this._buttonDeliverDisabledTemplate); 
            }
        }
        
        if (this._dataProcessor.isOrderCanceled(order)) {
            if (this._dataProcessor.isOrderCanBeCanceled(order)) {
                buttonHtml = Mustache.render(this._buttonCancelEnabledTemplate); 
            } else {
                buttonHtml = Mustache.render(this._buttonCancelDisabledTemplate); 
            }
        }

        return buttonHtml;
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
    
    Constr.prototype._cardStartPickButtonClick = function(button, event) {  
        var that = this;
        var card = $(button).closest(this._sel.card);
        var orderId = this._getOrderIdOnCard(card);
        var order = this._findOrder(orderId);
        if (!this._dataProcessor.isOrderCanBeStartPicking(order)) {
            return;
        }
                
        ++this._processedRequestsCount;
        this._backend.startCardInPick(orderId, 
            function(data) {
                that._startCardInPickSuccess(data);
                --that._requestsCount;
            },
            function() {
                that._showDefaultErrorMessage();
                --that._requestsCount;
            }
        );
    };
    
    Constr.prototype._cardDeliverButtonClick = function(button, event) {        
        var that = this;
        var card = $(button).closest(this._sel.card);
        var orderId = this._getOrderIdOnCard(card);
        var order = this._findOrder(orderId);
                
        if (!this._dataProcessor.isOrderCanBeDelivered(order)) {
            return;
        }
        
        ++this._requestsCount;
        this._backend.deliverCard(orderId,
            function(data) {
                that._cardDeliverSuccess(data);
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
        var card = $(button).closest(this._sel.card);
        var orderId = this._getOrderIdOnCard(card);
        var order = this._findOrder(orderId);
                
        if (!this._dataProcessor.isOrderCanBeCanceled(order)) {
            return;
        }
        
        ++this._requestsCount;
        this._backend.cancelCard(orderId,
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
        
        if (!this._dataProcessor.isOrderItemLogCanBeAdded(orderItemLog)) {
            return;
        }
                
        ++this._requestsCount;
        this._backend.setProductAdded(orderId, productId, 
            function(data) {
                that._setProductAddedSuccess(data);
                --that._requestsCount;
            },
            function() {
                that._showDefaultErrorMessage();
                --that._requestsCount;
            }
        );
    };
    
    Constr.prototype._updateCards = function()
    {
        var that = this;
        ++this._processedRequestsCount;
        this._backend.updateCards(this._cityId, 
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
    
    Constr.prototype._startCardInPickSuccess = function(data) {
        if (typeof data.success === 'undefined' || !data.success) {
            this._processUnsuccesOrderOperation(data);
            return;
        }
        
        this._updateExistCard(data.order);
        this._redrawCards();
    };
    
    Constr.prototype._cardDeliverSuccess = function(data)
    {
        if (typeof data.success === 'undefined' || !data.success) {
            this._processUnsuccesOrderOperation(data);
            return;
        }
        
        var orderId = data.orderId;
        this._deleteExistCard(orderId);
        $(this._sel.getCardSelector(orderId)).remove();
        this._redrawCards();
        
        // Откроем окно для печати чеков.
        var checksPrintUrl = this._getUrlFromTemplate(this._checksPrintUrlTemplate, {
            'orderId' : orderId
        });
        window.open(checksPrintUrl);
    };

    Constr.prototype._cardCancelSuccess = function(data)
    {   
        if (typeof data.success === 'undefined' || !data.success) {
            this._processUnsuccesOrderOperation(data);
            return;
        }

        var orderId = data.orderId;
        this._deleteExistCard(orderId);
        $(this._sel.getCardSelector(orderId)).remove();
        this._redrawCards();
    };
    
    
    Constr.prototype._setProductAddedSuccess = function(data) {
        if (typeof data.success === 'undefined' || !data.success) {
            this._processUnsuccesOrderOperation(data);
            return;
        }
        
        var order = data.order;
        this._updateExistCard(order);
        this._redrawCards();
    };
    
    Constr.prototype._processUnsuccesOrderOperation = function(data)
    {
        if (typeof data.success === 'undefined') {
            this._showDefaultErrorMessage();
            return;
        }
        
        if (data.success) {
            throw new Error();
        }
        
        if (typeof data.order === 'undefined') {
            this._showDefaultErrorMessage();
            return;
        }
        
        var order = data.order;       
        if (!this._dataProcessor.isOrderCorrect(order)) {
            this._deleteExistCard(order.id);
            this._redrawCards();
            return;
        } else {        
            this._updateExistCard(order);
            this._redrawCards();
            return;
        }
        
        return;
    };
          
    Constr.prototype._showDefaultErrorMessage = function() {
        alert('Произошла ошибка');
    };
    
    Constr.prototype._cleanCards = function() {
        for (var i = 0; i < this._cards.length; i++) {
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
        for (var i = 0; i < this._cards.length; i++) {
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
                return;
            }
        }
    };
    
    Constr.prototype._deleteExistCard = function(orderId) {
        for (var i = 0; i < this._cards.length; i++) {
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
                return;
            }
        }
    };
    
    Constr.prototype._updateCardsOnOrders = function(orders)
    {
        this._cleanCards();
        this._cards = [];
        for (var i = 0; i < orders.length; i++) {
            var card = {
                'order' : orders[i],
                'intervalHandler' : null
            };
            this._cards.push(card);
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
    
    Constr.prototype._getUrlFromTemplate = function(urlTemplate, params) {
        var url = urlTemplate;
        for (var paramName in params) {
            if (params.hasOwnProperty(paramName)) {
                var replacement = '__'+paramName+'__';
                var value = params[paramName];
                url = url.replace(replacement, value);
            }
        }
    
        return url;
    };
    
    
    return Constr;
    
})();