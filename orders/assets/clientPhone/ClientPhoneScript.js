var ClientPhoneScript = (function() {
    
    function init() {
        var selectors = {
            'clientPhone' : '[data-select="clientPhone"]',
            'clientAlterPhone' : '[data-select="clientAlterPhone"]',
            'phoneInput' : '[data-select="phoneInput"]',
            'alterPhoneInput' : '[data-select="alterPhoneInput"]',
            'fillPhoneButton' : '[data-select="fillPhoneButton"]',
            'fillAlterPhoneButton' : '[data-select="fillAlterPhoneButton"]',
        };
      
        $(selectors.fillPhoneButton).on('click', function() {
            var clientPhone = $(selectors.clientPhone).text();
            $(selectors.phoneInput).val(clientPhone);
        });
        
        $(selectors.fillAlterPhoneButton).on('click', function() {
            var clientAlterPhone = $(selectors.clientAlterPhone).text();
            $(selectors.alterPhoneInput).val(clientAlterPhone);
        });
    }
    
    return {
        'init' : init
    };

})();