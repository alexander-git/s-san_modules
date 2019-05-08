var CartController = (function() {
    
    var Constr = function() {
        this._sel = new CartSelectors();
    };
    
    Constr.prototype.init = function() {
       
        var that = this;
        
        $(document).on('change', this._sel.productCountInput, function(e) {
            that._productCountInputChange(this, e);
        });
        
        $(document).on('click', this._sel.productDeleteButton, function(e) {
            that._productDeleteButtonClick(this, e);
        });
        
    };
      
    Constr.prototype._productCountInputChange = function(countInput, event) {
        this._refreshValues();
    };
    
    Constr.prototype._productDeleteButtonClick = function(deleteButton, event) {
        deleteButton = $(deleteButton);
        var product = deleteButton.closest(this._sel.product);
        product.remove();
        this._refreshValues();
    };
    
    Constr.prototype._refreshValues = function() {
        var products = $(document).find(this._sel.product);
                
        var productsCount = products.length;
        var totalPrice = 0;
        for (var i = 0; i < products.length; i++) {
            var product = $(products[i]);
            var count = parseInt(product.find(this._sel.productCountInput).val());
            var price = parseInt(product.find(this._sel.productPrice).text());
            var prouductTotalPrice = count * price;
            $(product).find(this._sel.productTotalPrice).text(prouductTotalPrice);
            totalPrice += prouductTotalPrice;
        }
        $(this._sel.productsCount).text(productsCount);
        $(this._sel.totalPrice).text(totalPrice);
    };
     
    return Constr;
    
})();