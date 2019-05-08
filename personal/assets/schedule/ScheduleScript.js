var ScheduleScript = (function() {
    
    function init(params) {
        var scheduleController = new ScheduleController(params);
        scheduleController.init();
    }
    
    return {
        'init' : init
    };

})();