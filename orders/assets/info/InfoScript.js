var InfoScript = (function() {
    
    function init(params) {
        var infoController = new InfoController(params);
        infoController.init();
    }
    
    return {
        'init' : init
    };

})();