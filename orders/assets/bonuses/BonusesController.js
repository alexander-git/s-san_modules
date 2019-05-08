var BonusesController = (function() {
    
    var Constr = function(params) {
        this._totalPrice = parseInt(params.totalPrice);
        this._hasBonuscard = params.hasBonuscard;
        if (this._hasBonuscard) {
            this._bonuses = parseInt(params.bonuses); 
        }
        
        this._sel = new BonusesSelectors();
        this._backend = new BonusesBackend(params);
    };
    
    Constr.prototype.init = function() {
        var that = this;
        
        if (this._hasBonuscard) {
            $(this._sel.fillTaxByAllBonusesButton).on('click', function(e) {
                that._fillTaxByAllBonusesButtonClick(this, e);
            });
        
            $(this._sel.payByBonusesButton).on('click', function(e) {
                that._payByBonusesButtonClick(this, e);
            });
        }
          
        /*  
        // Промокоды.
        $(document).on('click', this._sel.sendCodeButton, function(e) {
            that._sendCodeButtonClick(this, e);
        });
        */
    };
      
    Constr.prototype._fillTaxByAllBonusesButtonClick = function(fillTaxByAllBonusesButton, event) {
        if (this._bonuses <= 0) {
            return;
        }
        var value = Math.min(this._totalPrice, this._bonuses);
        $(this._sel.taxInput).val(value);
    };
    
    Constr.prototype._payByBonusesButtonClick = function(payByBonusesButton, event) {
        var that = this;
        
        var tax = $(this._sel.taxInput).val();
        if (tax === '') {
            return;
        }
        
        this._backend.payByBonuses(
            tax,
            function(data) {
                that._payByBonusesSuccess(data);
            },
            function(data) {
                that._showDefaultErrorMessage();
            }
        );
    };
    
    /*
    // Промокоды.
    Constr.prototype._sendCodeButtonClick = function(sendCodeButton, event) {
        var that = this;

        var code = $(this._sel.codeInput).val();
        if (code === '') {
            return;
        }
        
        this._backend.sendCode(
            code,
            function(data) {
                that._sendCodeSuccess(data);
            },
            function(data) {
                that._showDefaultErrorMessage();
            }
        );
    };
    */
   
    Constr.prototype._payByBonusesSuccess = function(data)
    {
        if (data.success) {
            alert('Оплата прошла успешно');
            $(this._sel.bonusesValue).text(data.bonuses);
            this._bonuses = data.bonuses;
        } else {
            if (typeof data.errorMessage !== 'undefined') {
                this._showErrorMessage(data.errorMessage);
            } else {
                this._showDefaultErrorMessage();
            }
        }
    };
    
    /*
    // Промокоды.
    Constr.prototype._sendCodeSuccess = function(data) {
        console.log(data);
    };
    */
   
    Constr.prototype._showErrorMessage = function(errorMessage) {
        
    };
     
    Constr.prototype._showDefaultErrorMessage = function() {
        alert('Произошла ошибка');
    };
    
    return Constr;
    
})();