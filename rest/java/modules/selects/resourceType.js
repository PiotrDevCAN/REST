/**
 *
 */

let StaticResourceTypes = await cacheBustImport('./modules/dataSources/staticResourceTypes.js');

class resourceTypeSelect {

    constructor() {

    }

    async prepareDataForSelect(formId) {
        var data = await StaticResourceTypes.getResourceTypes();
        $('#' + formId + ' .resourceType').select2({
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
        $('#' + formId + ' .resourceType')
            .val(value)
            .trigger('change');
    }
}

const ResourceTypeSelect = new resourceTypeSelect();

export { ResourceTypeSelect as default };