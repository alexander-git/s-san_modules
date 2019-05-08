var ScheduleController = (function() {
    
    var Constr = function(params) {
        this._departmentId = params.departmentId;
        this._borderWidth = params.borderWidth;

        this._groupNameRowTemplate = null;
        this._rowTemplate = null;
        this._nameCellTemplate = null;
        this._cellTemplate = null;
        this._filledCellTemplate = null; 
        this._workTimeTemplate = null;
        
        // При открытии диалога для создания интервала будет 
        // устанавливаться в null. При обновлении интревала будет
        // устанавливаться id этого интервала.
        this._currentWorkTimeId = null;
        // При открытии диалога для создания интервала будет 
        // устанавливаться id пользователя. При обновлении интревала будет
        // устанавливаться в null.
        this._currentUserId = null;
       
        this._currentDate = null;
        
        // Используется для перерисовки интревалов при изменении размеров окна.
        this._resizeTimerId = null;

        this._sel = new ScheduleSelectors();
        this._items = new ScheduleItems();
        
        this._backend = new ScheduleBackend(params);
    };
    
    Constr.prototype.init = function() {
        this._initDateControls();
        this._initModalControls();
        this._prepareTemplates();
        this._load(this._getDateValue());
        
        var that = this;
        
        // Сделаем задержку в обработке события изменения размеров, чтобы 
        // реальная перерисовка происходила только тогда когда пользователь 
        // закончит перетаскивание.
        $(window).resize(function(e) {
            clearTimeout(that._resizeTimerId);
            that._resizeTimerId = setTimeout(function() {
                that._resizeEventHandler(e);
            }, 300);
        });
        
    };
      
    Constr.prototype._initDateControls = function() {
        this._items.previousDateButton = $(this._sel.previousDateButton);
        this._items.nextDateButton = $(this._sel.nextDateButton);
        this._items.dateInput = $(this._sel.dateInput);
     
        var that = this;
        
        this._bindChangeDateToDateInput();
    
        this._items.previousDateButton.on('click', function() {
            that._clickPreviousDateButton();
        });
        
        this._items.nextDateButton.on('click', function() {
            that._clickNextDateButton();
        });
    };
    
    Constr.prototype._initModalControls = function() {
        this._items.modal = $(this._sel.modal);
        this._items.timeInputFrom = $(this._sel.timeInputFrom);
        this._items.timeInputTo = $(this._sel.timeInputTo);
        this._items.timeSaveButton = $(this._sel.timeSaveButton);
        this._items.timeCancelButton = $(this._sel.timeCancelButton);
        this._items.timeInputErrorMessage = $(this._sel.timeInputErrorMessage);
    
        var that = this;
        
        this._items.timeSaveButton.on('click', function(e) {
            that._clickTimeSaveButton(this, e);
        });
        this._items.timeCancelButton.on('click', function(e) {
            that._clickTimeCancelButton(this, e);
        });
    };
      
    Constr.prototype._prepareTemplates = function() {
        this._groupNameRowTemplate = $(this._sel.groupNameRowTemplate).html();
        this._rowTemplate = $(this._sel.rowTemplate).html();
        this._nameCellTemplate = $(this._sel.nameCellTemplate).html();
        this._cellTemplate = $(this._sel.cellTemplate).html();
        this._workTimeTemplate = $(this._sel.workTimeTemplate).html();

        Mustache.parse(this._groupNameRowTemplate);
        Mustache.parse(this._rowTemplate);
        Mustache.parse(this._nameCellTemplate);
        Mustache.parse(this._cellTemplate);
        Mustache.parse(this._workTimeTemplate);
    };
    
    Constr.prototype._resizeEventHandler = function(event) {
        var rows = $(this._getAnyRowSelector());
        for (var i = 0; i < rows.length; i++) {
            var row = $(rows[i]);
            var userId = parseInt(row.attr('data-user-id'));
            
            var workTimeItems = row.find(this._getAnyWorkTimeSelector());
            
            var workTimes = [];
            for (var j = 0; j < workTimeItems.length; j++) {
                var workTimeItem = $(workTimeItems[j]);
                // Искуственно создадим объект workTime. Дату добавлять 
                // не будем, так как при отрисовке она не нужна.
                var workTime = {
                    'id' : parseInt(workTimeItem.attr('data-work-time-id')),
                    'user_id' : userId,
                    'from' : workTimeItem.attr('data-from'),
                    'to' : workTimeItem.attr('data-to')
                    
                };
                workTimes.push(workTime);
            }
            
            this._redrawRow(userId, workTimes);
        }
    };
    
    Constr.prototype._clickPreviousDateButton = function() {
        var date = this._decDayInDate(this._getDateValue());
        this._setDateValue(date);  
    };
    
    Constr.prototype._clickNextDateButton = function() {
        var date = this._incDayInDate(this._getDateValue());
        this._setDateValue(date);
    };
    
    Constr.prototype._clickCell = function(cell, event) {
        cell = $(cell);
        
        var timeIndex = parseInt(cell.attr('data-time'));
        
        // Вначале проверим есть ли в пределах ячейки уже созданный интервал.
        // Если это так, то имитируем клик по нему.
        var workTimesInRow = cell
            .closest(this._getAnyRowSelector())
            .find(this._getAnyWorkTimeSelector());
        for (var i = 0; i < workTimesInRow.length; i++) {
            var workTimeItem = $(workTimesInRow[i]);
            var begin = parseInt(workTimeItem.attr('data-begin'));
            var end = parseInt(workTimeItem.attr('data-end'));
            if (timeIndex >= begin && timeIndex <= end) {
                event.stopPropagation();
                workTimeItem.click();
                return;
            }
        }
        
        // Если интервала внутри ячейки нет, то откроем диалог для создания 
        // нового интервала.
        var fromTimeIndex = timeIndex;
        var toTimeIndex = fromTimeIndex + 1;
        if (toTimeIndex > 24) {
            toTimeIndex = 24;
        }

        this._items.timeInputFrom.val(this._createTimeForInputOnCellIndex(fromTimeIndex));
        this._items.timeInputTo.val(this._createTimeForInputOnCellIndex(toTimeIndex));
        this._items.timeInputErrorMessage.hide();
        
        this._currentWorkTimeId = null;
        this._currentUserId = parseInt(cell.attr('data-user-id'));
              
        this._showModal();
    };
    
    Constr.prototype._clickWorkTime = function(item, event) {
        event.stopPropagation();
        item = $(item);
        
        var from = item.attr('data-from');
        var to = item.attr('data-to');
    
        this._items.timeInputFrom.val(from);
        this._items.timeInputTo.val(to);
        this._items.timeInputErrorMessage.hide();
        
        this._currentWorkTimeId = parseInt(item.attr('data-work-time-id'));
        this._currentUserId = null;
        
        this._showModal();
    };
    
    Constr.prototype._clickTimeSaveButton = function(button, event) {
        var from = this._items.timeInputFrom.val();
        var to = this._items.timeInputTo.val();
        
        if (!this._validateTimeValues(from, to)) {
            return;
        }
        
        var that = this;
        
        var params = {
            'from' : from,
            'to' : to
        };
        
        $(this._sel.timeInputErrorMessage).hide();
        
        var itIsUpdate = this._currentWorkTimeId !== null;
        if (itIsUpdate) {
            params['workTimeId'] = this._currentWorkTimeId;
            this._backend.updateWorkTime(
                params, 
                function(data) {
                    that._updateWorkTimeSuccess(data);
                },
                function(data) {
                    that._showDefaultErrorMessage();
                }
            );
        } else {
            params['date'] = this._currentDate;
            params['userId'] = this._currentUserId;
            
            this._backend.createWorkTime(
                params, 
                function(data) {
                    that._createWorkTimeSuccess(data);
                },
                function(data) {
                    that._showDefaultErrorMessage();
                }
            );
        }
    };
    
    Constr.prototype._clickTimeCancelButton = function(button, event) {
        this._closeModal();
    };
    
    
    Constr.prototype._clickDeleteWorkTime = function(item, event) {
        event.stopPropagation();
        
        if (!confirm('Вы уверены?')) {
            event.preventDefault();
            return;
        }

        var button = $(item);
        var workTimeId = button.attr('data-work-time-id');
        
        var that = this;
        
        this._backend.deleteWorkTime(workTimeId, 
            function(data) {
                that._deleteWorkTimeSuccess(data);
            }, 
            function(data) {
                that._showDefaultErrorMessage();
            }
        );    
    };
    
    Constr.prototype._createTimeForInputOnCellIndex = function(cellIndex) {
        var result = '';
        if (cellIndex < 10) {
           result = '0' + cellIndex;
        } else {
            result = cellIndex;
        }
        result += ':'+'00';
        return result;
    };
    
    Constr.prototype._validateTimeValues = function(fromStr, toStr) {
        var timeRegExp = /^[0-9]{2}:[0-9]{2}$/;
        if (!timeRegExp.test(fromStr)) {
            alert('Начально время ввдено неверно!');
            return false;
        }
        if (!timeRegExp.test(toStr)) {
            alert('Конечное время ввдено неверно!');
            return false;
        }
        var from = this._parseTimeString(fromStr);
        var to = this._parseTimeString(toStr);
        
        if (from.hours > 23 || from.minutes > 59) {
            alert('Начально время ввдено неверно!');
            return false;
        }
        if (to.hours > 24 || to.minutes > 59) {
            alert('Конечное время ввдено неверно!');
            return false;
        }
        if (to.hours === 24 && to.minutes > 0) {
            alert('Конечное время ввдено неверно!');
            return false;
        }
        
        var fromFullMinutes = from.hours * 60 + from.minutes;
        var toFullMinutes = to.hours * 60 + to.minutes;
        if (fromFullMinutes >= toFullMinutes) {
            alert('Начальное время должно быть меньше конечного!');
            return false;
        }
        
        return true;
    };

        
    Constr.prototype._load = function(date) {
        var that = this;
        this._backend.load(
            this._departmentId, 
            date, 
            function(data) {
                that._loadSuccess(data, date);
            },
            function(data) {
                that._showDefaultErrorMessage();
            }
        );
    };
   
    Constr.prototype._loadSuccess = function(data, date) {
        this._currentDate = date;
        
        var bodySelector = this._sel.body;
        var body = $(bodySelector);
        
        body.html('');
        
        var allUserIds = [];
        
        var groups = data.groups;
        for (var i = 0; i < groups.length; i++) {
            var group = groups[i];
            var gropNameRow = Mustache.render(this._groupNameRowTemplate, {
                'groupName' : group.name
            });
            $(gropNameRow).appendTo(bodySelector);
           
            var users = group.users;
            for (var j = 0; j < users.length; j++) {
                var userId = users[j].id;
                var userName = users[j].name;
                allUserIds.push(userId);
                
                var rowSelector = this._getRowSelector(userId);
                var row = Mustache.render(this._rowTemplate, {
                    'userId' : userId
                });
                $(row).appendTo(bodySelector);
                
                var nameCell = Mustache.render(this._nameCellTemplate, {
                    'name' : userName,
                    'userId': userId,
                });
               
                $(nameCell).appendTo(rowSelector);
                this._drawTimeCells(userId);
            }   
        }
        this._updateAllAmountTimes(allUserIds, data.workTimes);
        this._drawWorkTimes(data.workTimes);
    };
    
    Constr.prototype._createWorkTimeSuccess = function(data) {
        this._saveWorkTimeSuccuss(data);
    };

    Constr.prototype._updateWorkTimeSuccess = function(data) {
        this._saveWorkTimeSuccuss(data);
    };
    
    Constr.prototype._saveWorkTimeSuccuss = function(data) {
        if (typeof data.errorMessage !== 'undefined') {
            $(this._sel.timeInputErrorMessage).html(data.errorMessage).show();
            return;
        }
        this._redrawRow(data.userId, data.workTimes);
        this._closeModal();
    };
    
    Constr.prototype._deleteWorkTimeSuccess = function(data) {
        this._redrawRow(data.userId, data.workTimes);
    };
         
    Constr.prototype._showDefaultErrorMessage = function() {
        alert('Произошла ошибка');
    };

    Constr.prototype._drawTimeCells = function(userId) {
        var rowSelector = this._getRowSelector(userId);
        var that = this;
        
        for (var i = 0; i <= 23; i++) {
            var cell = Mustache.render(this._cellTemplate, {
                'userId' : userId,
                'time' : i
            });
            $(cell).appendTo(rowSelector).on('click', function(e) {
                that._clickCell(this, e);
            });
        }
    };
    
    Constr.prototype._drawWorkTimes = function(workTimes) {
        for (var i = 0; i < workTimes.length; i++) {
            this._drawWorkTime(workTimes[i]);
        }
    };
    
    Constr.prototype._removeWorkTimes = function(userId) {
        var rowSelector = this._getRowSelector(userId);      
        $(rowSelector).find(this._getAnyWorkTimeSelector()).remove();
    };
    
    Constr.prototype._redrawRow = function(userId, workTimes) {
        this._removeWorkTimes(userId);
        this._updateAmountTime(userId, workTimes);
        this._drawWorkTimes(workTimes);
    };
    
    Constr.prototype._drawWorkTime = function(workTime) {
        var userId = workTime.user_id;
        var workTimeId = workTime.id;
        var fromStr = workTime.from;
        var toStr = workTime.to;
        
        var from = this._parseTimeString(fromStr);
        var to = this._parseTimeString(toStr);
        var blockParams = this._getWorkTimeBlockParams(from, to);
                
        var timeIndex = blockParams.beginCell;
        var cellSelector = this._getCellSelector(userId, timeIndex);
        var begin = blockParams.beginCell;
        var end = blockParams.endCell;
        
        var workTimeRendered = Mustache.render(this._workTimeTemplate, {
            'userId' : userId,
            'workTimeId' : workTimeId,
            'from' : fromStr,
            'to' : toStr,
            'begin' : begin,
            'end' : end
        });
        
        var that = this;
        
        var workTimeContainerSelector = this._getWorkTimeContainerSelector(userId, timeIndex);
        
        $(workTimeRendered)
            .css('left', blockParams.left+'%')
            .appendTo(workTimeContainerSelector);
            
        var workTimeSelector = this._getWorkTimeSelector(userId, fromStr, toStr);
        
        var beginCellSelector = this._getCellSelector(userId, begin);
        var endCellSelector = this._getCellSelector(userId, end);
        
        var left = $(beginCellSelector).offset().left;
        var right = $(endCellSelector).offset().left + $(endCellSelector).width();
        // Длинна занимаемая всеми ячейками внутри которых будет интервал.
        var fullWidth = right - left + this._borderWidth;
        
        var width = Math.round(((blockParams.minutes/blockParams.fullMinutes) * fullWidth));
        $(workTimeSelector).css('width', width+'px');
        
        $(workTimeSelector).on('click', function(e) {
            that._clickWorkTime(this, e);
        });
        
        $(cellSelector).find(this._sel.deleteWorkTime).on('click', function(e) {
            that._clickDeleteWorkTime(this, e);
        });
    };
    
    Constr.prototype._updateAllAmountTimes = function(userIds, workTimes) {
        var amountTimes = {};
        for (var i = 0; i < userIds.length; i++) {
            var userId = userIds[i];
            amountTimes[userId] = 0;
        }
        
        for (var i = 0; i < workTimes.length; i++) {
            var workTime = workTimes[i];
            var userId = workTime.user_id;
            amountTimes[userId] += this._getWorkTimeMinutes(workTime); 
        }
        
        for (var userId in amountTimes) {
            if (!amountTimes.hasOwnProperty(userId)) {
                continue;
            }
            var minutes = amountTimes[userId];
            var amountTimeSelector = this._getAmountTimeSelector(userId);
            var amountStr = this._getAmountTimeStrOnMinutes(minutes);
            $(amountTimeSelector).html(amountStr);
        }
    };
    
    Constr.prototype._updateAmountTime = function(userId, workTimes) {
        var minutes = 0;
        for (var i = 0; i < workTimes.length; i++) {
            var workTime = workTimes[i];
            minutes += this._getWorkTimeMinutes(workTime); 
        }
        var amountTimeSelector = this._getAmountTimeSelector(userId);
        var amountStr = this._getAmountTimeStrOnMinutes(minutes);
        $(amountTimeSelector).html(amountStr);
    };
     
    Constr.prototype._parseTimeString = function(timeStr) {
        var matches;
        var hoursStr;
        var minutesStr;
        var hours;
        var minutes;

        var timeRegExp = /([0-9]{2}):([0-9]{2})/i;
        var matches = timeRegExp.exec(timeStr);
        var hoursStr = matches[1];
        var minutesStr = matches[2];
        
        if (hoursStr[0] === '0') {
            hours = parseInt(hoursStr[1]);
        } else {
            hours = parseInt(hoursStr);
        }
        
        if (minutesStr[0] === '0') {
            minutes = parseInt(minutesStr[1]);
        } else {
            minutes = parseInt(minutesStr);
        }
        
        return {
            'hours' : hours,
            'minutes' : minutes
        };
    };
    
    Constr.prototype._getWorkTimeBlockParams = function(from, to) {
        var allHours = to.hours - from.hours;
        if (to.minutes > 0) {
            ++allHours;
        }
        var fullMinutes = allHours * 60;
        
        var beginCell = from.hours;
        var endCell;
        if (to.hours === 24) {
            endCell = 23;
        } else {
           endCell = to.hours;
           if (to.minutes === 0) {
               --endCell;
            }
        }
        
        var cellCount = allHours;
   
        
        var minutes = 
            (60 - from.minutes) + 
            (to.hours - from.hours - 1) * 60 +
            to.minutes;
    
        // Вычисление длинны и отступа когда ячейки объединяются.
        //var width = Math.round((minutes/fullMinutes) * 100);
        //var left = Math.round(from.minutes/fullMinutes * 100);
        
        // Вычисление длинны и отступа когда ячейки не объединяются.
        // В этом случае нет возможности учесть границы таблицы.
        //var width = Math.round((minutes/60) * 100);
        var left = Math.round((from.minutes/60) * 100);
    
        return {
            'cellCount' : cellCount,
            'beginCell' : beginCell,
            'endCell' : endCell,
            //'width' : width,
            'left' : left,
            'minutes' : minutes,
            'fullMinutes' : fullMinutes
        };
    };
    
    Constr.prototype._getWorkTimeMinutes = function(workTime) {
        var from = this._parseTimeString(workTime.from);
        var to = this._parseTimeString(workTime.to);
        return this._getWorkTimeBlockParams(from, to).minutes;
    };
    
    Constr.prototype._getAmountTimeStrOnMinutes = function(minutes) {
        var hours = Math.floor(minutes / 60);
        var restMinutes = minutes - (hours * 60);
        var result = '';
        if (hours !== 0) {
            result += hours+' ч.';
        }
        if (restMinutes !== 0) {
            if (result !== '') {
                result += ' ';
            }
            result += restMinutes+' мин.';
        }
        
        if (result === '') {
            result = '0 ч.';
        }
        
        return result;
    };
    
    Constr.prototype._incDayInDate = function(date) {
        var copy = this._getDateCopy(date);
        copy.setDate(copy.getDate() + 1);
        return copy;
    };
    
    Constr.prototype._decDayInDate = function(date) {
        var copy = this._getDateCopy(date);
        copy.setDate(copy.getDate() - 1);
        return copy;
    };
    
    Constr.prototype._getDateCopy = function(date) {
        var copy = new Date();
        copy.setFullYear(date.getFullYear());
        copy.setMonth(date.getMonth());
        copy.setDate(date.getDate());
        copy.setHours(date.getHours());
        copy.setMinutes(date.getMinutes());
        copy.setSeconds(date.getSeconds());
        copy.setMilliseconds(date.getMilliseconds());
        return copy;
    };
    
    Constr.prototype._getAnyRowSelector = function () {
        return '[data-select-row]';
    };
    
    Constr.prototype._getAnyCellSelector = function() {
        return '[data-select-cell]';
    };
    
    Constr.prototype._getAnyWorkTimeContainerSelector = function() {
        return '[data-select-work-time-container]';
    };
    
    Constr.prototype._getAnyWorkTimeSelector = function() {
        return '[data-select-work-time]';
    };
    
    Constr.prototype._getAnyAmountTimeSelector = function() {
        return '[data-select-amount-time]';
    };
    
    Constr.prototype._getRowSelector = function(userId) {
        return '[data-select-row="'+userId+'"]';
    };
   
    Constr.prototype._getCellSelector = function(userId, timeIndex) {
        return '[data-select-cell="'+userId+'_'+timeIndex+'"]';
    };
    
    Constr.prototype._getWorkTimeContainerSelector = function(userId, timeIndex) {
        return '[data-select-work-time-container="'+userId+'_'+timeIndex+'"]';
    };
    
    Constr.prototype._getWorkTimeSelector = function(userId, from, to) {
        return '[data-select-work-time="'+userId+'_'+from+'_'+to+'"]';
    };

    Constr.prototype._getAmountTimeSelector = function(userId) {
        return '[data-select-amount-time="'+userId+'"]';
    };

    Constr.prototype._bindChangeDateToDateInput = function() {
        var that = this;
        this._items.dateInput.kvDatepicker().on('changeDate', function(e) {
            that._load(e.date);
        });
    };
    
    Constr.prototype._setDateValue = function(date) {
        this._items.dateInput.kvDatepicker('setDate', date);
    };
    
    Constr.prototype._getDateValue = function() {
        return this._items.dateInput.kvDatepicker('getDate');
    };
    
    Constr.prototype._showModal = function() {
        this._items.modal.modal('show');
    };
    
    Constr.prototype._closeModal = function() {
        this._items.modal.modal('hide');
    };
    
    return Constr;
    
})();