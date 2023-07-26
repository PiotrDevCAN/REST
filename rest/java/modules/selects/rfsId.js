/**
 *
 */

let RFSs = await cacheBustImport('./modules/dataSources/staticRFSs.js');

class refIdSelect {

    constructor() {

    }

    async prepareDataForSelect(formId) {
        var data = await RFSs.getRFSs();
        $('#'+formId+' .RFSId').select2({
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
        $('#' + formId + ' .RFSId')
            .val(value)
            .trigger('change');
    }
}

const RefIdSelect = new refIdSelect();
// await RefIdSelect.prepareDataForSelect();

export { RefIdSelect as default };