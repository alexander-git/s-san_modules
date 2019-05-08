var StationPickBackend = (function() {
    
    var Constr = function(config) {
        this._getCardsUrlTemplate = config.getCardsUrlTemplate;
        this._updateCardsUrlTemplate = config.updateCardsUrlTemplate;
        this._startCardInPickUrlTemplate = config.startCardInPickUrlTemplate;
        this._deliverCardUrlTemplate = config.deliverCardUrlTemplate;
        this._cancelCardUrlTemplate = config.cancelCardUrlTemplate;
        this._setProductAddedUrlTemplate = config.setProductAddedUrlTemplate;
        this._backend = new Backend();
    };
    
    Constr.prototype.getCards = function(cityId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._getCardsUrlTemplate, {
            'cityId' : cityId
        });
        
        this._backend.performGetRequest(url, success, error);
    };
    
    Constr.prototype.updateCards = function(cityId, success, error)
    {
        var url = this._backend.getUrlFromTemplate(this._updateCardsUrlTemplate, {
            'cityId' : cityId
        });
        
        this._backend.performGetRequest(url, success, error);
    };
    
    Constr.prototype.startCardInPick = function(orderId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._startCardInPickUrlTemplate, {
            'orderId' : orderId  
        });
        
        this._backend.performPostRequest(url, success, error);
    };
    
    Constr.prototype.deliverCard = function(orderId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._deliverCardUrlTemplate, {
            'orderId' : orderId  
        });
        
        this._backend.performPostRequest(url, success, error);
    };
         
    Constr.prototype.cancelCard = function(orderId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._cancelCardUrlTemplate, {
            'orderId' : orderId  
        });
        
        this._backend.performPostRequest(url, success, error);
    };
    
    Constr.prototype.setProductAdded = function(orderId, productId, success, error) {
        var url = this._backend.getUrlFromTemplate(this._setProductAddedUrlTemplate, {
            'orderId' : orderId,
            'productId' : productId
        });
        
        this._backend.performPostRequest(url, success, error);
    };
         
    return Constr;

})();