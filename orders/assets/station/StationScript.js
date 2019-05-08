var StationScript = (function() {
    
    function init(params) {
        var stationController = new StationController(params);
        stationController.init();
    }
    
    return {
        'init' : init
    };

})();