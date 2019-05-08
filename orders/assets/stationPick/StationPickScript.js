var StationPickScript = (function() {
    
    function init(params) {
        var stationPickController = new StationPickController(params);
        stationPickController.init();
    }
    
    return {
        'init' : init
    };

})();