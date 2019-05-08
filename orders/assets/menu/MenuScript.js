var MenuScript = (function() {
    
    function init(params) {
        var menuController = new MenuController(params);
        menuController.init();
    }
    
    return {
        'init' : init
    };

})();