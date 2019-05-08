var StationPickSelectors = function() {
    this.currentTime = '[data-select="stationPick__currentTime"]';
    this.cardsContainer = '[data-select="stationPick__cardsContainer"]';

    this.cardTemplate = '[data-select="stationPick__cardTemplate"]';
    
    this.headerTemplate = '[data-select="stationPick__headerTemplate"]';
   
    this.productKitchenNewTemplate = '[data-select="stationPick__productKitchenNewTemplate"]';
    this.productKitchenPreparingTemplate = '[data-select="stationPick__productKitchenPreparingTemplate"]';
    this.productKitchenPreparedTemplate = '[data-select="stationPick__productKitchenPreparedTemplate"]';
    this.productKitchenCompleteTemplate = '[data-select="stationPick__productKitchenCompleteTemplate"]';
    this.productKitchenAddedTemplate = '[data-select="stationPick__productKitchenAddedTemplate"]';
    this.productKitchenCanceledTemplate = '[data-select="stationPick__productKitchenCanceledTemplate"]';
    
    this.pickLabelTemplate = '[data-select="stationPick__pickLabelTemplate"]';
    
    this.productPickNewTemplate = '[data-select="stationPick__productPickNewTemplate"]';
    this.productPickPreparingTemplate = '[data-select="stationPick__productPickPreparingTemplate"]';
    this.productPickAddedTemplate = '[data-select="stationPick__productPickAddedTemplate"]';
    
    this.personNumTemplate = '[data-select="stationPick__personNumTemplate"]';

    this.footerTimeTemplate = '[data-select="stationPick__footerTimeTemplate"]';
    
    this.buttonStartPickEnabledTemplate = '[data-select="stationPick__buttonStartPickEnabledTemplate"]';
    this.buttonStartPickDisabledTemplate = '[data-select="stationPick__buttonStartPickDisabledTemplate"]';
    this.buttonDeliverEnabledTemplate = '[data-select="stationPick__buttonDeliverEnabledTemplate"]';
    this.buttonDeliverDisabledTemplate = '[data-select="stationPick__buttonDeliverDisabledTemplate"]';
    this.buttonCancelEnabledTemplate = '[data-select="stationPick__buttonCancelEnabledTemplate"]';
    this.buttonCancelDisabledTemplate = '[data-select="stationPick__buttonCancelDisabledTemplate"]';
    
    this.card = '[data-select-card-pick]';
    this.cardHeaderContainer = '[data-select="cardPick__headerContainer"]';
    this.cardBodyContainer = '[data-select="cardPick__bodyContainer"]';
    this.cardFooterTimeContainer = '[data-select="cardPick__footerTimeContainer"]';
    this.cardFooterButtonContainer = '[data-select="cardPick__footerButtonContainer"]';
    
    this.cardStartPickButton = '[data-select="cardPick__startPickButton"]';
    this.cardDeliverButton = '[data-select="cardPick__deliverButton"]';
    this.cardCancelButton = '[data-select="cardPick__cancelButton"]';

    this.cardTimer = '[data-select="cardPick__timer"]';
    
    this.cardProduct = '[data-select-product]';
    
    this.getCardSelector = function(orderId) {
        return '[data-select-card-pick="'+orderId+'"]';
    };
    
    this.getProductSelector = function(orderId, productId) {
        return '[data-select-product="'+orderId+'_'+productId+'"]';
    };
    
};