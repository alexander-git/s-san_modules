var ClientNameScript = (function() {
    
    function init() {
        var selectors = {
            'clientName' : '[data-select="clientName"]',
            'recipientInput' : '[data-select="recipientInput"]',
            'fillRecipientButton' : '[data-select="fillRecipientButton"]'
        };
      
        $(selectors.fillRecipientButton).on('click', function() {
            var clientName = $(selectors.clientName).text();
            $(selectors.recipientInput).val(clientName);
        });
    }
    
    return {
        'init' : init
    };

})();