var StationSelectors = function() {
    this.currentTime = '[data-select="station__currentTime"]';
    this.cardsContainer = '[data-select="station__cardsContainer"]';
    
    this.cardTemplate = '[data-select="station__cardTemplate"]';
    
    this.headerDefaultTemplate = '[data-select="station__headerDefaultTemplate"]';
    this.headerInWorkTemplate = '[data-select="station__headerInWorkTemplate"]';
    this.headerCanceledTemplate = '[data-select="station__headerCanceledTemplate"]';
    
    this.productNewTemplate = '[data-select="station__productNewTemplate"]';
    this.productPreparingTemplate = '[data-select="station__productPreparingTemplate"]';
    this.productPreparedTemplate = '[data-select="station__productPreparedTemplate"]';
    
    this.infoDefaultTemplate = '[data-select="station__infoDefaultTemplate"]';
    this.infoCanceledTemplate = '[data-select="station__infoCanceledTemplate"]';
    this.infoPreparingTemplate = '[data-select="station__infoPreparingTemplate"]';
    this.infoPreparedTemplate = '[data-select="station__infoPreparedTemplate"]';
    
    this.card = '[data-select-card]';
    this.cardHeaderContainer = '[data-select="card__headerContainer"]';
    this.cardBodyContainer = '[data-select="card__bodyContainer"]';
    this.cardInfoContainer = '[data-select="card__infoContainer"]';
    
    this.cardInWorkButton = '[data-select="card__inWorkButton"]';
    this.cardCancelButton = '[data-select="card__cancelButton"]';
    this.cardCompleteButton = '[data-select="card__completeButton"]';
    this.cardInfoTimer = '[data-select="card__infoTimer"]';
    
    this.cardProduct = '[data-select-product]';
    
    this.getCardSelector = function(orderId) {
        return '[data-select-card="'+orderId+'"]';
    };
    
    this.getProductSelector = function(orderId, productId) {
        return '[data-select-product="'+orderId+'_'+productId+'"]';
    };
    
};