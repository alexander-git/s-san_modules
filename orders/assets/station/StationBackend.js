var StationBackend = (function() {
    
    var Constr = function(config) {
        this._getCardsUrlTemplate = config.getCardsUrlTemplate;
        this._updateCardsUrlTemplate = config.updateCardsUrlTemplate;
        this._startCardInWorkUrlTemplate = config.startCardInWorkUrlTemplate;
        this._completeCardUrlTemplate = config.completeCardUrlTemplate;
        this._cancelCardUrlTempate = config.cancelCardUrlTemplate;
        this._setProductPreparingUrlTemplate = config.setProductPreparingUrlTemplate;
        this._setProductPreparedUrlTemplate = config.setProductPreparedUrlTemplate;
    
        this._backend = new Backend();
    };
    
    Constr.prototype.getCards = function(cityId, stationId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._getCardsUrlTemplate, {
            'cityId' : cityId,
            'stationId' : stationId
        });
        
        this._backend.performGetRequest(url, success, error);
    };
    
    Constr.prototype.updateCards = function(cityId, stationId, orderIds, success, error)
    {
        var url = this._backend.getUrlFromTemplate(this._updateCardsUrlTemplate, {
            'cityId' : cityId,
            'stationId' : stationId
        });
        
        var data;
        if (orderIds === null || orderIds.length === 0) {
            data = null;
        } else {
            data = {
               'orderIds' : orderIds
            };
        }
        
        this._backend.performGetRequest(url, success, error, data);
    };
    
    Constr.prototype.startCardInWorkUrl = function(orderId, stationId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._startCardInWorkUrlTemplate, {
            'orderId' : orderId,
            'stationId' : stationId
        });
        
        this._backend.performPostRequest(url, success, error);
    };
    
    Constr.prototype.completeCard = function(orderId, stationId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._completeCardUrlTemplate, {
            'orderId' : orderId,
            'stationId' : stationId
        });
                
        this._backend.performPostRequest(url, success, error);
    };
    
    Constr.prototype.cancelCard = function(orderId, stationId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._cancelCardUrlTempate, {
            'orderId' : orderId,
            'stationId' : stationId
        });
        
        this._backend.performPostRequest(url, success, error);
    };
    
    Constr.prototype.setProductPreparing = function(orderId, productId, success, error)
    {
        var url = this._backend.getUrlFromTemplate(this._setProductPreparingUrlTemplate, {
            'orderId' : orderId,
            'productId' : productId
        });
        
        this._backend.performPostRequest(url, success, error);
    }
    
    Constr.prototype.setProductPrepared = function(orderId, productId, success, error)
    {
        var url = this._backend.getUrlFromTemplate(this._setProductPreparedUrlTemplate, {
            'orderId' : orderId,
            'productId' : productId
        });
        
        this._backend.performPostRequest(url, success, error);
    };
        
    return Constr;

})();