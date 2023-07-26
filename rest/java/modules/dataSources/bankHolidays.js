/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/bankHolidays.js');

class bankHolidays {

    eventsRaw = [];
    eventsTitles = [];
    events = [];

    mainKey = 'england-and-wales';
    subKey = 'events';
    dateKey = 'date';
    titleKey = 'title';

    constructor() {
    
    }

    async getEvents() {
        // await for API data
        var dates = await APIData[this.mainKey][this.subKey];
        dates.forEach((date, index) => {
            var dateStr = date[this.dateKey];
            this.eventsRaw.push(dateStr);
        });
        return this.eventsRaw;
    }

    async getFormattedEvents() {
        // await for API data
        var dates = await APIData[this.mainKey][this.subKey];
        dates.forEach((date, index) => {
            var dateStr = date[this.dateKey];
            var dateObj = new Date(dateStr);
            this.events.push(dateObj.toDateString());
        });
        return this.events;
    }

    async getEventTitles() {
        // await for API data
        var dates = await APIData[this.mainKey][this.subKey];
        dates.forEach((date, index) => {
            var dateStr = date[this.dateKey];
            var dateTitle = date[this.titleKey];
            this.eventsTitles[dateStr] = dateTitle;
        });
        return this.eventsTitles;
    }

    async getEventTitle(date) {
        var titles = await this.getEventTitles();
        var title = titles[date];
        return title;
    }

    _getArrayFromDate(date) {
        var dateArr = date.split('-');
        return dateArr;
    }

    getYearFromDate(date) {
        var dateArr = this._getArrayFromDate(date);
        var year = parseInt(dateArr[0]);
        return year;
    }
    
    getMonthFromDate(date) {
        var dateArr = this._getArrayFromDate(date);
        var month = parseInt(dateArr[1]) - 1;
        return month;
    }
    
    getDayFromDate(date) {
        var dateArr = this._getArrayFromDate(date);
        var day = parseInt(dateArr[2]);
        return day;
    }
}

const BankHolidays = new bankHolidays();

export { BankHolidays as default };
