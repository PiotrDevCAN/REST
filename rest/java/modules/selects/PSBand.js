/**
 *
 */

let StaticPSBands = await cacheBustImport('./modules/dataSources/staticPSBands.js');

class PSBandSelect {

    constructor() {

    }

    async prepareDataForSelect(formId) {
        var data = await StaticPSBands.getPSBands();
        $('#' + formId + ' .PSBand').select2({
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
        $('#' + formId + ' .PSBand')
            .val(value)
            .trigger('change');
    }
}

const PsBandSelect = new PSBandSelect();

export { PsBandSelect as default };