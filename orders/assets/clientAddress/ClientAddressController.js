var ClientAddressController = (function() {
    
    var Constr = function(params) {
        this._sel = new ClientAddressSelectors();
        this._cityName = params.cityName;
    };
    
    Constr.prototype.init = function() {
        var that = this;
        
        this._initKladrForStreetInput();
        this._initInputs();
        
        $(this._sel.fillCodeByHomePhoneButton).on('click', function(e) {
            that._fillCodeByHomePhoneButtonClick(this, e);
        });
       
        $(this._sel.fillEntranceButton).on('click', function(e) {
            that._fillEntanceButtonClick(this, e);
        });
        
        that._inputChange();
       
    };
      
    Constr.prototype._initKladrForStreetInput = function() {
        var that = this;
        
        // Сначала получим id города.
        var cityTypeCode = 1;
        var query = {
            'query' : this._cityName,
            'type' : $.kladr.type.city,
            'typeCode' : cityTypeCode,
            'limit' : 1,
            'withParent' : 0
        };
        
        $.kladr.api(query, function(data) {
            var cityId = null;
            
            if (data.length > 0 &&  typeof data[0].id !== 'undefined') {
                cityId = parseInt(data[0].id); 
            }
            
            if (cityId !== null) {
                //Привяжем автодополнение улицы к input.
                $(that._sel.streetInput).kladr({
                    'type' : $.kladr.type.street,
                    'withParent' : 0,
                    'parentType' : $.kladr.type.city,
                    'parentId' : cityId
                });
            }
        });
    };
    
    Constr.prototype._initInputs = function() {
        var that = this;
        var inputSelectors = this._sel.getInputSelectors();
        for (var i = 0; i < inputSelectors.length; i++) {
            $(inputSelectors[i]).on('change', function(input, event) {
                that._inputChange();
            });
        }
    };
    
    Constr.prototype._inputChange = function() {
        var fullInfo = '';
        var street = $(this._sel.streetInput).val();
        var home = $(this._sel.homeInput).val();
        var appartment = $(this._sel.appartmentInput).val();
        var floor = $(this._sel.floorInput).val();
        var entrance = $(this._sel.entranceInput).val();
        var code = $(this._sel.codeInput).val();
        var comment = $(this._sel.commentInput).val();
        
        var firstPart = '';
        
        if (street !== '') {
            firstPart += ', ул. '+street;
        }
        if (home !== '') {
            firstPart+= ', д. '+home;
        }
        if (appartment !== '') {
            firstPart += ', кв. '+appartment;
        }
        if (floor !== '') {
            if (appartment === '') {
                firstPart += ',';
            }
            firstPart += ' этаж '+floor;
        }
        
        var secondPart = '';
        if (entrance !== '') {
            secondPart += ' '+entrance+ ' подъезд';
        }
        if (code !== '') {
            if (secondPart !== '') {
                secondPart += ',';
            }
            secondPart += ' код: '+code;
        }
        if (comment !== '') {
            if (secondPart !== '') {
                secondPart += ',';
            }
            secondPart += ' '+comment;
        }
        
        fullInfo = firstPart;
        if (secondPart !== '') {
            // Если вторая половина адреса не пустая отделим её от первой точкой.
            fullInfo += '.'+secondPart;
        }
        
        $(this._sel.fullInfo).text(fullInfo);
    };
     
     
    Constr.prototype._fillCodeByHomePhoneButtonClick = function(button, event) {
        var value = $(this._sel.appartmentInput).val();
        $(this._sel.codeInput).val(value);
        this._inputChange();
    };
    
    Constr.prototype._fillEntanceButtonClick = function(button, event) {
        var value = this._getFillButtonValue(button);
        $(this._sel.entranceInput).val(value);
        this._inputChange();
    };
    
    Constr.prototype._getFillButtonValue = function(button) {
        return $(button).attr('data-value');
    };
    
    return Constr;
    
})();