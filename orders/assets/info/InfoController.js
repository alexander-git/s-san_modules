var InfoController = (function() {
    
    var Constr = function(params) {
        this._sel = new InfoSelectors();
    };
    
    Constr.prototype.init = function() {
        var that = this;
        $(this._sel.fillReturnSumButton).on('click', function(e) {
            that._fillReturnSumButtonClick(this, e);
        });
        
        $(this._sel.fillDeliveryDateButton).on('click', function(e) {
            that._fillDeliveryDateButtonClick(this, e);
        });
       
       $(this._sel.fillHourDeliveryTimeButton).on('click', function(e) {
           that._fillHourDeliveryTimeButtonClick(this, e);
       });
       
       $(this._sel.fillMinuteDeliveryTimeButton).on('click', function(e) {
           that._fillMinuteDeliveryTimeButtonClick(this, e);
       });
       
       $(this._sel.isPostponedInput).on('change', function(e) {
           that._isPostponedInputChange();
       });
       
       // Сразу обновим интерфейс в зависимости от состояния флажка.
       this._isPostponedInputChange();
    };
      
    Constr.prototype._fillReturnSumButtonClick = function(button, event) {
        var value = this._getFillButtonValue(button);
        $(this._sel.returnSumInput).val(value);
    };
    
    Constr.prototype._fillDeliveryDateButtonClick = function(button, event) {
        var value = this._getFillButtonValue(button);
        $(this._sel.deliveryDateInput).val(value);
    };
    
    Constr.prototype._fillHourDeliveryTimeButtonClick  = function(button, event) {
        var value = this._getFillButtonValue(button);
        var input = $(this._sel.deliveryTimeInput);

        var inputValue = input.val();
        var matches = this._getDeliveryTimeInputValueMatches();
        var newValue = '';
        if (inputValue === '' || matches === null) {
            newValue = this._getTimeStr(value, '00');
        } else {
            newValue = this._getTimeStr(value, matches.minutes);
        }
        
        input.val(newValue);
    };
    
    Constr.prototype._fillMinuteDeliveryTimeButtonClick = function(button, event) {
        var value = this._getFillButtonValue(button);
        var input = $(this._sel.deliveryTimeInput);
        var inputValue = input.val();
        var matches = this._getDeliveryTimeInputValueMatches();
        var newValue = '';
        if (inputValue === '' || matches === null) {
            newValue = this._getTimeStr('00', value);
        } else {
            newValue = this._getTimeStr(matches.hours, value);
        }
        
        input.val(newValue);
    };
    
    Constr.prototype._getTimeStr = function(hoursStr, minutesStr) {
        return hoursStr+':'+minutesStr;
    };
    
    Constr.prototype._isPostponedInputChange = function() {
        var checkbox = $(this._sel.isPostponedInput);
        var postponedOrderInfo = $(this._sel.postponedOrderInfo);
        if (!checkbox.is(':checked')) {
            //$(this._sel.deliveryDateInput).val('');
            //$(this._sel.deliveryTimeInput).val('');
            postponedOrderInfo.hide();
        } else {
            postponedOrderInfo.show();
        }
    };
    
    Constr.prototype._getDeliveryTimeInputValueMatches = function() {
        var input = $(this._sel.deliveryTimeInput);
  
        var inputValue = input.val();
        var timeRegExp = /([0-9]{2}):([0-9]{2})/i;
        var matches = timeRegExp.exec(inputValue);
        if (matches === null) {
            return null;
        } 
        
        return {
            'hours' : matches[1],
            'minutes' : matches[2]
        };
    };

    
    
    Constr.prototype._getFillButtonValue = function(button) {
        return $(button).attr('data-value');
    };
    
    return Constr;
    
})();