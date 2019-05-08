var ClientAddressScript = (function() {
    
    function init(params) {
        var clientAddressController = new ClientAddressController(params);
        clientAddressController.init();
    }
    
    return {
        'init' : init
    };

})();