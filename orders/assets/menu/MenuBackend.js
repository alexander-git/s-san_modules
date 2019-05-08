var MenuBackend = (function() {
    
    var Constr = function(config) {
        this._addToCartUrl = config.addToCartUrl;
        this._backend = new Backend();
    };
    
    Constr.prototype.addToCart = function(productId, success, error) {
        var url = this._addToCartUrl;
        var data = {
            'productId' : productId
        };
        this._backend.performPostRequest(url, success, error, data);
    };
    

    return Constr;

})();