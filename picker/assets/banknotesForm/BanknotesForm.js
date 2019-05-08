var BanknotesForm = (function() {
    
    var Constr = function(modelName, sumSelector) {
        this._modelName = modelName;
        this._sumSelector = sumSelector;
        this._formFields = [];
    };
    
    // Хранит пару "имя свойства" - "множитель". Множитель будет использоваться 
    // при подсчёте суммы.
    Constr.prototype.FIELDS = {
        'count_5000' : 5000,
        'count_1000' : 1000,
        'count_500' : 500,
        'count_100' : 100,
        'count_50' : 50,
        'rest' : 1
    };
    
    Constr.prototype.init = function() {
        var that = this;
        
        for (var fieldName in this.FIELDS) {
            if (this.FIELDS.hasOwnProperty(fieldName)) {
                var selector = this._getFiledSelector(fieldName);
                var formField = $(selector);
                
                formField.on('change', function() {
                    that._onChangeFormField();
                });
                
                this._formFields[fieldName] = formField;
            }
        }
        
        this._onChangeFormField(); // Сразу обновим сумму.
    };
    
    Constr.prototype._getFiledSelector = function(fieldName) {
        return '[name="'+this._modelName+'['+fieldName+']'+'"]';
    };
    
    Constr.prototype._onChangeFormField = function() {
        var sum = 0;
        for (var fieldName in this._formFields) {
            var formField = this._formFields[fieldName];
            var factor = this.FIELDS[fieldName]; // Множитель.
            
            var value = parseInt(formField.val());
            if (!isNaN(value)) {
                sum += value * factor;
            }
        }
        $(this._sumSelector).text(sum);
    };
    
    return Constr;
    
})();