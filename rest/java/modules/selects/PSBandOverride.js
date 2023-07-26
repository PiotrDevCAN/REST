/**
 *
 */

class PSBandOverrideSelect {

    constructor() {

    }

    async prepareDataForSelect(formId) {
        var data = {
            'Yes': 'Yes',
            'No': 'No'
        };
        $('#' + formId + ' .PSBandOverride').select2({
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
        $('#' + formId + ' .PSBandOverride')
            .val(value)
            .trigger('change');
    }
}

const PsBandOverrideSelect = new PSBandOverrideSelect();

export { PsBandOverrideSelect as default };