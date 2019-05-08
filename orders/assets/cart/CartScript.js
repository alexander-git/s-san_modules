var CartScript = (function() {
    
    function init(params) {
        var cartController = new CartController(params);
        cartController.init();
    }
    
    return {
        'init' : init
    };

})();