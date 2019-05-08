var StationPickDataProcessor = (function() {
    
    var Constr = function(orderStageIds, orderItemLogStates, stationPickId) {
        // Вызовем конструктор базового класса.
        StationDataProcessorBase.call(this, orderStageIds, orderItemLogStates);
        this._stationPickId = stationPickId;
    };
    
     // Унаследуем от базового класса.
    Constr.prototype = Object.create(StationDataProcessorBase.prototype);
    Constr.prototype.constructor = StationDataProcessorBase;
    
    
    Constr.prototype.isOrderCorrect = function(order) {
        if (this.isOrderNew(order)) {
            return false;
        }
        if (this.isOrderDelivering(order)) {
            return false;
        }
        if (this.isOrderDelivered(order)) {
            return false;
        }
        if (typeof order.orderItemLogs === 'undefined') {
            return false;
        } 
        if (order.orderItemLogs.length === 0) {
            return false;
        }
        
        var orderItemLogs = order.orderItemLogs;
        for (var i = 0; i < orderItemLogs.length; i++) {
            if (!this.isOrderItemLogCanceled(orderItemLogs[i])) {
                return true;
            }
        }
        
        return true;
    };
    
    Constr.prototype.isOrderDefault = function(order) {
        if (this.isOrderAccepted(order) || this.isOrderInWork(order)) {
            for (var i = 0; i < order.orderItemLogs.length; i++) {
                if (!this.isOrderItemLogCanceled(order.orderItemLogs[i])) {
                    return true;
                }
            }
        }
        
        return false;
    };
    
    Constr.prototype.isOrderInPick = function(order) {
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            if (order.orderItemLogs[i].date_pick_start !== null) {
                return true;
            }
        }
       
        return false;
    };
    
    
    Constr.prototype.isOrderCanBeDelivered = function(order) {
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            if (!this.isOrderItemLogAdded(order.orderItemLogs[i])) {
                return false;
            }
        }
       
        return true;
    };
    
    Constr.prototype.isOrderCanBeStartPicking = function(order) {
        var kitchenOrderItemLogs = this.getKitchenOrderItemLogs(order);
        var pickOrderItemLogs = this.getPickOrderItemLogs(order);
        if (kitchenOrderItemLogs.length === 0  && pickOrderItemLogs.length !== 0) {
            return true;
        }
        // Должна быть хотя бы одна позиция со статусом complete.
        if (kitchenOrderItemLogs.length > 0) {
            for (var i = 0; i < kitchenOrderItemLogs.length; i++) {
                if (this.isOrderItemLogComplete(kitchenOrderItemLogs[i])) {
                    return true;
                }
            }
        }
        
        
        return false;
    };  
    
    Constr.prototype.isOrderCanBeCanceled = function(order) {
        if (!this.isOrderCanceled(order)) {
            return false;
        }
        var kitchenOrderItemLogs = this.getKitchenOrderItemLogs(order);
        if (kitchenOrderItemLogs.length === 0) {
            return true;
        }
        for (var i = 0; i < kitchenOrderItemLogs.length; i++) {
            if (
                this.isOrderItemLogPreparing(kitchenOrderItemLogs[i]) || 
                this.isOrderItemLogPrepared(kitchenOrderItemLogs[i])
            ) {
                return false;
            }
        }

        return true;
    };
    
    Constr.prototype.isOrderItemLogCanBeAdded = function(orderItemLog) {
        if (this.isKitchenOrderItemLog(orderItemLog)) {
            if (this.isOrderItemLogComplete(orderItemLog)) {
                return true;
            } else {
                return false;
            }
        } else if (this.isPickOrderItemLog(orderItemLog)) {
            if (this.isOrderItemLogPreparing(orderItemLog)) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Error('Неверное состояние заказа');
        }
    };
    
    Constr.prototype.getKitchenOrderItemLogs = function(order) {
        if (typeof order.orderItemLogs === 'undefined') {
            return [];
        }
        
        var result = [];
        var orderItemLog; 
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            var orderItemLog = order.orderItemLogs[i];
            if (this.isKitchenOrderItemLog(orderItemLog)) {
                result.push(orderItemLog);
            }
        }
        
        return result;
    };
   
    Constr.prototype.getPickOrderItemLogs = function(order) {
        if (typeof order.orderItemLogs === 'undefined') {
            return [];
        }
        
        var result = [];
        var orderItemLog; 
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            var orderItemLog = order.orderItemLogs[i];
            if (this.isPickOrderItemLog(orderItemLog)) {
                result.push(orderItemLog);
            }
        }
        
        return result;
    };
    
    Constr.prototype.isKitchenOrderItemLog = function(orderItemLog) {
        if (orderItemLog.station !== this._stationPickId) {
            return true;
        } else {
            return false;
        }
    };
   
    Constr.prototype.isPickOrderItemLog = function(orderItemLog) {
        if (orderItemLog.station === this._stationPickId) {
            return true;
        } else {
            return false;
        }
    };
    
    
    Constr.prototype.getOrderDateInWorkStart = function(order) {
        var min = null;
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            min = this._getMinOrNull(order.orderItemLogs[i].date_start, min);
        }
        
        return min;
    };
    
    Constr.prototype._getMinOrNull = function(a, b) {
        if (a === null) {
            return b;
        } 
        if (b === null) {
            return a;
        }
        if (a < b) {
            return a;
        } else {
            return b;
        }
    };

    return Constr;
    
})();