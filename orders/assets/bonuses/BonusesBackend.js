var BonusesBackend = (function() {
    
    var Constr = function(config) {
        this._payByBonusesUrl = config.payByBonusesUrl;
        this._sendCodeUrl = config.sendCodeUrl;
        this._backend = new Backend();
    };
    
    Constr.prototype.payByBonuses = function(tax, success, error)
    {
        var url = this._payByBonusesUrl;
        var data = {
            'tax' : tax
        };
        this._backend.performPostRequest(url, success, error, data);
    };
    
    /*
    // Промокоды.
    Constr.prototype.sendCode = function(code, success, error) {
        var url = this._sendCodeUrl;
        var data = {
            'code' : code
        };
        this._backend.performPostRequest(url, success, error, data);
    };
    */
    
    
    return Constr;

})();