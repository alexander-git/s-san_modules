var MenuController = (function() {
    
    var Constr = function(params) {
        this._sel = new MenuSelectors();
        this._backend = new MenuBackend(params);
    };
    
    Constr.prototype.init = function() {
       
        var that = this;

        $(document).on('change', this._sel.option, function(e) {
            that._optionChange(this, e);
        });
        
        $(document).on('click', this._sel.addButton, function(e) {
            that._addButtonClick(this, e);
        });
    };
      
    Constr.prototype._optionChange = function(option, event) {
        option = $(option);
        var product = option.closest(this._sel.product);
        var optionKey = this._getOptionKey(product);
        var priceValue = product.attr(this._getPriceAttrName(optionKey));    
        var price = product.find(this._sel.price);
        price.text(priceValue);
    };
    
    Constr.prototype._addButtonClick = function(addButton, event) {
        addButton = $(addButton);
        var product = addButton.closest(this._sel.product);
        
        var hasOptions = parseInt(product.attr(this._getHasOptionsAttrName()));
        var productId;        
        if (hasOptions === 1) {
            var optionKey = this._getOptionKey(product);
            productId = product.attr(this._getOptionIdAttrName(optionKey));    
        } else {
            productId = product.attr(this._getProductIdAttrName());
        }

        var that = this;
        this._backend.addToCart(
            productId,
            function(data) {
                that._addToCartSuccess(data);
            },
            function(data) {
                that._showDefaultErrorMessage();
            }
        );
    };
    
    Constr.prototype._getOptionKey = function(product) {
        var optionsContainer = product.find(this._sel.optionsContainer);
        var selectedOptions = optionsContainer.find(this._sel.option).filter(':checked');
        
        var selectedValues = [];
        for (var i = 0; i < selectedOptions.length; i++) {
            var selectedOption = $(selectedOptions[i]);
            selectedValues.push(selectedOption.val());
        }
        
        var optionKey = '';
        for (var i = 0; i < selectedValues.length - 1; i++) {
            optionKey += selectedValues[i]+'-';
        }
        optionKey += selectedValues[selectedValues.length - 1];
        
        return optionKey;
    };
    
    Constr.prototype._addToCartSuccess = function(data) {
        $(this._sel.cartTotalPrice).text(data.totalPrice);
        $(this._sel.cartProductsCount).text(data.productsCount);
    };
    
    Constr.prototype._showDefaultErrorMessage = function() {
        alert('Произошла ошибка');
    };
    
    Constr.prototype._getProductIdAttrName = function() {
        return 'data-product-id';
    };
    
    Constr.prototype._getHasOptionsAttrName = function() {
        return 'data-has-options';
    };
    
    
    Constr.prototype._getPriceAttrName = function(optionKey) {
        return 'data-price-'+optionKey;
    };
    
    Constr.prototype._getOptionIdAttrName = function(optionKey) {
        return 'data-option-id-'+optionKey;
    };
    
    
    return Constr;
    
})();