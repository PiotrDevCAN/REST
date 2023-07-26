/**
 *
 */

let formatResourceName = await cacheBustImport('./modules/functions/formatResourceName.js');
let VBACActiveResources = await cacheBustImport('./modules/dataSources/vbacActiveResources.js');

class resourceNameSelect {

    constructor() {

    }

    async prepareDataForSelect(formId) {
        var data = await VBACActiveResources.getActiveResources();
        $('#' + formId + ' .resourceName').select2({
            data: data,
            templateResult: formatResourceName
        });
    }

    initialise() {
        
    }

    selectValue(value, formId) {
        $('#' + formId + ' .resourceName')
            .val(value)
            .trigger('change');
    }
}

const ResourceNameSelect = new resourceNameSelect();

export { ResourceNameSelect as default };