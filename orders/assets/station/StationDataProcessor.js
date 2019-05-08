var StationDataProcessor = (function() {
    
    var Constr = function(orderStageIds, orderItemLogStates) {
        // Вызовем конструктор базового класса.
        StationDataProcessorBase.call(this, orderStageIds, orderItemLogStates);
    };
    
    // Унаследуем от базового класса.
    Constr.prototype = Object.create(StationDataProcessorBase.prototype);
    Constr.prototype.constructor = StationDataProcessorBase;
 
 
    Constr.prototype.isOrderDefault = function(order, stationId) {
        if (this.isOrderNew(order) || this.isOrderAccepted(order)) {
            return true;
        } 
        if (
            this.isOrderInWork(order) && 
            !this.areProductsOnStationInWork(order, stationId)
        ) {
            return true;
        }
        
        return false;
    };
    
    Constr.prototype.isOrderOnStationInWork = function(order, stationId) {
        if (
            this.isOrderInWork(order) && 
            this.areProductsOnStationInWork(order, stationId)
        ) {
            return true;
        }
    };
    
    Constr.prototype.areProductsOnStationInWork = function(order, stationId) 
    {
        if (typeof order.orderItemLogs === 'undefined') {
            return false;
        }
        
        var orderItemLog;
        var productsOnStationCount = 0;
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            orderItemLog = order.orderItemLogs[i];
            if (orderItemLog.station === stationId) {
                ++productsOnStationCount;
                if (
                    !this.isOrderItemLogPreparing(orderItemLog) &&
                    !this.isOrderItemLogPrepared(orderItemLog)
                ) {

                    return false;
                }
            }
        }
        
        if (productsOnStationCount > 0) {
            return true;
        } else {
            return false;
        }
    };
    
    Constr.prototype.getProductsStationNumber = function(order, stationId) {
        if (typeof order.orderItemLogs === 'undefined') {
            return null;
        }
        
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            var orderItemLog = order.orderItemLogs[i];
            
            if (orderItemLog.station === stationId) {
                if (
                    typeof orderItemLog.number === 'undefined' ||
                    orderItemLog.number === '' ||
                    orderItemLog.number === null
                ) {
                    return null;
                } else {
                    return orderItemLog.number;
                }
            }
        }
        
        return null;
    };
    
    Constr.prototype.getProductsDateStart = function(order, stationId) {
        if (typeof order.orderItemLogs === 'undefined') {
            return null;
        }
        
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            var orderItemLog = order.orderItemLogs[i];
            
            if (orderItemLog.station === stationId) {
                if (
                    typeof orderItemLog.date_start === 'undefined' ||
                    orderItemLog.date_start === '' ||
                    orderItemLog.date_start === null
                ) {
                    return null;
                } else {
                    return orderItemLog.date_start;
                }
            }
        }
        
        return null;
    };
    
    Constr.prototype.areAllProductsPrepared = function(order) {
        if (typeof order.orderItemLogs === 'undefined') {
            return false;
        }
        
        for (var i = 0; i < order.orderItemLogs.length; i++) {
            var orderItemLog = order.orderItemLogs[i];
            if (!this.isOrderItemLogPrepared(orderItemLog)) {
                return false;
            }
        }
        
        return true;
    };
    
    return Constr;
    
})();