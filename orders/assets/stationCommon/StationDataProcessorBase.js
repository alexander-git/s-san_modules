var StationDataProcessorBase = (function() {
    
    var Constr = function(orderStageIds, orderItemLogStates) {
        this._orderStageIds = orderStageIds;
        this._orderItemLogStates = orderItemLogStates;
    };
    
    Constr.prototype.isOrderNew = function(order) {
        return order.stage_id === parseInt(this._orderStageIds.new);
    };

    Constr.prototype.isOrderAccepted = function(order) {
        return order.stage_id === parseInt(this._orderStageIds.accepted);
    };
     
    Constr.prototype.isOrderInWork = function(order) {
        return order.stage_id === parseInt(this._orderStageIds.inWork);
    };
    
    Constr.prototype.isOrderDelivering = function(order) {
        return order.stage_id === parseInt(this._orderStageIds.delivering);
    };
    
    Constr.prototype.isOrderDelevered = function(order) {
        return order.stage_id === parseInt(this._orderStageIds.delevered);
    };
        
    Constr.prototype.isOrderCanceled = function(order) {
        return order.stage_id === parseInt(this._orderStageIds.canceled);
    };
    
    Constr.prototype.isOrderItemLogNew = function(orderItemLog) {
        return orderItemLog.state === parseInt(this._orderItemLogStates.new);
    };
    
    Constr.prototype.isOrderItemLogPreparing = function(orderItemLog) {
        return orderItemLog.state === parseInt(this._orderItemLogStates.preparing);
    };

    Constr.prototype.isOrderItemLogPrepared = function(orderItemLog) {
        return orderItemLog.state === parseInt(this._orderItemLogStates.prepared);
    };
    
    Constr.prototype.isOrderItemLogComplete = function(orderItemLog) {
        return orderItemLog.state === parseInt(this._orderItemLogStates.complete);
    };
    
    Constr.prototype.isOrderItemLogAdded = function(orderItemLog) {
        return orderItemLog.state === parseInt(this._orderItemLogStates.added);
    };
    
    Constr.prototype.isOrderItemLogEnd = function(orderItemLog) {
        return orderItemLog.state === parseInt(this._orderItemLogStates.end);
    };
    
    Constr.prototype.isOrderItemLogCanceled = function(orderItemLog) {
        return orderItemLog.state === parseInt(this._orderItemLogStates.canceled);
    };
    
    return Constr;
    
})();