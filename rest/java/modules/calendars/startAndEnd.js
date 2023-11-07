/**
 *
 */

let helper = await cacheBustImport('./modules/helper.js');
let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');

class startAndEnd {

    static dateFormat = 'YYYY-MM-DD';
    static dateFormat2 = 'D MMM YYYY';

    events;
    eventsRaw;
    eventTitles;

    startFieldId;
    endFieldId;

    startPicker;    // Pikaday object
    endPicker;      // Pikaday object

    startDateDefault;   // String
    endDateDefault;     // String

    startDateDefaultDate;   // Date object
    endDateDefaultDate;     // Date object

    // currently selected dates
    startDateMoment;  // Moment object
    endDateMoment;    // Moment object

    onDrawCallback(events, titles) {
        helper.addEventTitlesToPicker(events, titles);
    }

    updateStartDate(date) {
        this.startPicker.setStartRange(date);
        this.endPicker.setStartRange(date);
        this.endPicker.setMinDate(date);
    }

    updateEndDate(date) {
        this.startPicker.setMaxDate(date);
        this.startPicker.setEndRange(date);
        this.endPicker.setEndRange(date);
    }

    updateMaxDate(date) {
        this.startPicker.setMaxDate(date);
        this.startPicker.gotoDate(date);

        this.endPicker.setDate(date);
        this.endPicker.setMaxDate(date);
        this.endPicker.gotoDate(date);
    }

    initPickers() {

        var $this = this;
        let eventsPromise = BankHolidays.getFormattedEvents().then((response) => {
            $this.events = response;
        });
        let eventsRawPromise = BankHolidays.getEvents().then((response) => {
            $this.eventsRaw = response;
        });
        let eventTitlesPromise = BankHolidays.getEventTitles().then((response) => {
            $this.eventTitles = response;
        });

        const promises = [eventsPromise, eventsRawPromise, eventTitlesPromise];
        Promise.allSettled(promises)
            .then((results) => {
                $this.initialiseDateFields();
            });
    }

    initialiseDateFields() {

        // read initial value of START date field
        var startDateField = document.getElementById(this.startFieldId);
        if (startDateField !== null) {
            this.startDateDefault = startDateField.getAttribute('value');
            if (this.startDateDefault !== null && this.startDateDefault !== '') {
                this.startDateDefaultDate = new Date(this.startDateDefault);
            }
        }

        // read initial value of END date field
        var endDateField = document.getElementById(this.endFieldId);
        if (endDateField !== null) {
            this.endDateDefault = endDateField.getAttribute('value');
            if (this.endDateDefault !== null && this.endDateDefault !== '') {
                this.endDateDefaultDate = new Date(this.endDateDefault);
            }
        }

        var $this = this;
        let startPickerPromise = new Promise((resolve, reject) => {
            this.startPicker = new Pikaday({
                events: $this.events,
                firstDay: 1,
                field: document.getElementById('Input' + $this.startFieldId),
                format: startAndEnd.dateFormat2,
                showTime: false,
                minDate: new Date(),

                defaultDate: $this.startDateDefaultDate,
                setDefaultDate: $this.startDateDefaultDate !== null,

                onSelect: function () {
                    $this.startDateMoment = this.getMoment();
                    var db2Value = $this.startDateMoment.format(startAndEnd.dateFormat);
                    var relatedField = document.getElementById($this.startFieldId);
                    if (relatedField !== null) {
                        relatedField.setAttribute('value', db2Value);
                    }
                    $this.updateStartDate($this.startDateMoment.toDate());
                },
                onClose: function () {
                    var dateWas = $this.startDateDefault;
                    var dateCurrent = $this.startDateMoment.format(startAndEnd.dateFormat);
                    if (dateWas != dateCurrent) {
                        if ($this.endDateDefault !== '') {
                            if ($this.startDateMoment.isAfter($this.endDateMoment)) {
                                // restore previous start date
                                this.setDate(dateWas);
                                dateCurrent = dateWas;
                            }
                        }
                        $this.startDateDefault = dateCurrent;
                    }
                },
                onDraw: this.onDrawCallback($this.eventsRaw, $this.eventTitles)
            });
            resolve('Success');
        });

        let endPickerPromise = new Promise((resolve, reject) => {
            this.endPicker = new Pikaday({
                events: $this.events,
                firstDay: 1,
                field: document.getElementById('Input' + $this.endFieldId),
                format: startAndEnd.dateFormat2,
                showTime: false,
                minDate: new Date(),

                defaultDate: $this.endDateDefaultDate,
                setDefaultDate: $this.endDateDefaultDate !== null,

                onSelect: function () {
                    $this.endDateMoment = this.getMoment();
                    var db2Value = $this.endDateMoment.format(startAndEnd.dateFormat);
                    var relatedField = document.getElementById($this.endFieldId);
                    if (relatedField !== null) {
                        relatedField.setAttribute('value', db2Value);
                    }
                    $this.updateEndDate($this.endDateMoment.toDate());
                },
                onClose: function () {
                    var dateWas = $this.endDateDefault;
                    var dateCurrent = $this.startDateMoment.format(startAndEnd.dateFormat);
                    if (dateWas != dateCurrent) {
                        if ($this.startDateDefault !== '') {
                            if ($this.startDateMoment.isAfter($this.endDateMoment)) {
                                // restore previous end date
                                this.setDate(dateWas);
                                dateCurrent = dateWas;
                            }
                        }
                        $this.endDateDefault = dateCurrent;
                    }
                },
                onDraw: this.onDrawCallback($this.eventsRaw, $this.eventTitles)
            });
            resolve('Success');
        });

        const promises = [startPickerPromise, endPickerPromise];
        Promise.allSettled(promises)
            // Promise.all(promises)
            .then((results) => {
                results.forEach((result) => console.log(result.status));

                this.startPicker = this.startPicker;
                this.endPicker = this.endPicker;

                var _startDate = this.startPicker.getMoment();
                var _endDate = this.endPicker.getMoment();

                if (_startDate) {
                    this.startDateMoment = _startDate;
                    var relatedField = document.getElementById($this.startFieldId);
                    if (relatedField !== null) {
                        this.startDateDefault = relatedField.getAttribute('value');
                    }
                }
                if (typeof this.startDateDefaultDate !== 'undefined') {
                    this.updateStartDate(this.startDateMoment.toDate());
                }

                if (_endDate) {
                    this.endDateMoment = _endDate;
                    var relatedField = document.getElementById($this.endFieldId);
                    if (relatedField !== null) {
                        this.endDateDefault = relatedField.getAttribute('value');
                    }
                }
                if (typeof this.endDateDefaultDate !== 'undefined') {
                    this.updateEndDate(this.endDateMoment.toDate());
                }
            });
    }

    destroyPickers() {
        this.startPicker.clear();
        this.startPicker.destroy();
        this.endPicker.clear();
        this.endPicker.destroy();
    }

    constructor(startFieldId, endFieldId) {
        // set picker fields ids
        this.startFieldId = startFieldId;
        this.endFieldId = endFieldId;
    }
}

export { startAndEnd as default };