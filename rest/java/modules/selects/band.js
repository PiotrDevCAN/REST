/**
 *
 */

let StaticBands = await cacheBustImport('./modules/dataSources/staticBands.js');

class bandSelect {

    constructor() {

    }

    async prepareDataForSelect(formId) {
        var data = await StaticBands.getBands();
        $('#' + formId + ' .band').select2({
            data: data,
            tags: true,
            createTag: function (params) {
                return undefined;
            }
        });
    }

    initialise() {
        
    }

    selectValue(value, formId) {
        $('#' + formId + ' .band')
            .val(value)
            .trigger('change');
    }
}

const BandSelect = new bandSelect();

export { BandSelect as default };