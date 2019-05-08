var ScheduleSelectors = function() {
    this.body = '[data-select="schedule__body"]'; 
    this.previousDateButton = '[data-select="schedule__previousDateButton"]';
    this.nextDateButton = '[data-select="schedule__nextDateButton"]';
    this.dateInput = '[data-select="schedule__dateInput"]';
    
    this.modal = '[data-select="schedule__modal"]';
    this.timeInputFrom = '[data-select="schedule__timeInputFrom"]';
    this.timeInputTo = '[data-select="schedule__timeInputTo"]';
    this.timeSaveButton = '[data-select="schedule__timeSaveButton"]';
    this.timeCancelButton = '[data-select="schedule__timeCancelButton"]';
    this.timeInputErrorMessage = '[data-select="schedule__timeInputErrorMessage"]';
    
    this.groupNameRowTemplate = '[data-select="schedule__groupNameRowTemplate"]';
    this.rowTemplate = '[data-select="schedule__rowTemplate"]';
    this.nameCellTemplate = '[data-select="schedule__nameCellTemplate"]';
    this.cellTemplate = '[data-select="schedule__cellTemplate"]';
    this.workTimeTemplate = '[data-select="schedule__workTimeTemplate"]';

    this.deleteWorkTime = '[data-select="schedule__deleteWorkTime"]';
};