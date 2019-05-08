var BonusesScript = (function() {
    
    function init(params) {
        var bonusesController = new BonusesController(params);
        bonusesController.init();
    }
    
    return {
        'init' : init
    };


})();