/**
 *
 */

let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');

class picker {

    container;
    fieldId;

    onSelectCallback;
    onCloseCallback;
    onDrawCallback;

    onSelect() {
        console.log(this.container);
    };

    onClose() {
        console.log(this.container);
    };

    onDraw() {
        console.log(this);
        // this.onDrawCallback();
    };

    async initPicker(onSelect, onClose, onDraw) {

        let events = await BankHolidays.getFormattedEvents();

        this.onSelectCallback = onSelect;
        this.onCloseCallback = onClose;
        this.onDrawCallback = onDraw;

        this.picker = new Pikaday({
            events: events,
            firstDay: 1,
            field: document.getElementById('Input' + this.fieldId),
            format: 'D MMM YYYY',
            showTime: false,
            onSelect: this.onSelect,
            onClose: this.onClose,
            onDraw: this.onDraw
        });
    }

    constructor(parent, fieldId) {
        this.fieldId = fieldId;
        this.container = parent;
    }
}

export { picker as default };