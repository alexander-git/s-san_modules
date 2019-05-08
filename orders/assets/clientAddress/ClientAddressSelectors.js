var ClientAddressSelectors = function() {
    this.streetInput = '[data-select="address__streetInput"]';
    this.homeInput = '[data-select="address__homeInput"]';
    this.appartmentInput = '[data-select="address__appartmentInput"]';
    this.floorInput = '[data-select="address__floorInput"]';
    this.entranceInput = '[data-select="address__entranceInput"]';
    this.codeInput = '[data-select="address__codeInput"]';
    this.commentInput = '[data-select="address__commentInput"]';
  
    this.fullInfo = '[data-select="address__fullInfo"]';
    this.fillEntranceButton = '[data-select="fillEntranceButton"]';
    this.fillCodeByHomePhoneButton = '[data-select="fillCodeByHomePhoneButton"]';
    
    this.getInputSelectors = function () {
        return [
            this.streetInput,
            this.homeInput,
            this.appartmentInput, 
            this.floorInput, 
            this.entranceInput,
            this.codeInput, 
            this.commentInput
        ];
    };
};