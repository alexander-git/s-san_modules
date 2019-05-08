var BanknotesFormScript = (function() {
    
    function init(modelName, sumSelector) {
        var banknotesForm = new BanknotesForm(modelName, sumSelector);
        banknotesForm.init();
    }
    
    return {
        'init' : init
    };

})();