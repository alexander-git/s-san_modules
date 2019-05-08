var ScheduleBackend = (function() {
    
    var Constr = function(config) {
        this._loadUrlTemplate = config.loadUrlTemplate;
        this._createWorkTimeUrlTemplate = config.createWorkTimeUrlTemplate;
        this._upadateWorkTimeUrlTemplate = config.updateWorkTimeUrlTemplate;
        this._deleteWorkTimeUrlTemplate = config.deleteWorkTimeUrlTemplate;
    };
    
    Constr.prototype.load = function(departmentId, date, success, error) {
        date = this._getRequestDateValue(date);
        var url = this._getLoadUrl(departmentId, date);
        this._performGetRequest(url, success, error);
    };
    
    Constr.prototype.createWorkTime = function(params, success, error) {
        if (
            typeof params.date === 'undefined' ||
            typeof params.userId === 'undefined' ||
            typeof params.from === 'undefined' ||
            typeof params.to === 'undefined'
        ) {
            throw new Error();
        }
        var url = this._getCreateWorkTimeUrl();
        var data = {
            'date' : this._getRequestDateValue(params.date),
            'userId' : params.userId,
            'from' :  params.from,
            'to' : params.to
        };
        this._performPostRequest(url,success, error, data);
    };
    
    
    Constr.prototype.updateWorkTime = function(params, success, error) {
        if (
            typeof params.workTimeId === 'undefined' ||
            typeof params.from === 'undefined' ||
            typeof params.to === 'undefined'
        ) {
            throw new Error();
        }
        
        var url = this._getUpdateWorkTimeUrl(params.workTimeId);
        var data = {
            'from' :  params.from,
            'to' : params.to
        };
        this._performPostRequest(url, success, error, data);
    };
    
    
    Constr.prototype.deleteWorkTime = function(workTimeId, success, error) {
        var url = this._getDeleteWorkTimeUrl(workTimeId);
        this._performPostRequest(url, success, error);
    };
    
    Constr.prototype._performGetRequest = function(url, success, error, data) {
        if (data === undefined) {
            data = null;
        }
        
        $.ajax({
            'url' : url,
            'success' : success,
            'error' : error,
            'method' : 'GET',
            'data' : data,
            'dataType' : 'json',
            'cache' : false
        });  
    };

    Constr.prototype._performPostRequest = function(url, success, error, data) {
        $.ajax({
            'url' : url,
            'success' : success,
            'error' : error,
            'method' : 'POST',
            'data' : data,
            'dataType' : 'json'
        });
    };
    
    Constr.prototype._getRequestDateValue = function(date) {
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        
        if (day < 10) {
            day = '0' + day;
        } 
        if (month < 10) {
            month = '0' + month;
        } 
        
        return day+'-'+month+'-'+year;
    };
    
    Constr.prototype._getLoadUrl = function(departmentId, date) {
        var url = this._loadUrlTemplate;
        url = url.replace('__departmentId__', departmentId);
        url = url.replace('__date__', date);        
        return url;
    };
    
    Constr.prototype._getCreateWorkTimeUrl = function() {
        return this._createWorkTimeUrlTemplate;
    };
    
    Constr.prototype._getUpdateWorkTimeUrl = function(workTimeId) {
        var url = this._upadateWorkTimeUrlTemplate;
        url = url.replace('__workTimeId__', workTimeId);
        return url;
    };
    
    Constr.prototype._getDeleteWorkTimeUrl = function(workTimeId) {
        var url = this._deleteWorkTimeUrlTemplate;
        url = url.replace('__workTimeId__', workTimeId);
        return url;
    };

    return Constr;

})();