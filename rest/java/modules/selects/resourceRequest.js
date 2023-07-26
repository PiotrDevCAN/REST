/**
 *
 */

let ResponceRequests = await cacheBustImport('./modules/dataSources/staticResourceRequests.js');

class resourceRequestSelect {

    constructor() {

    }

    async prepareDataForSelect(formId) {
        var data = await ResponceRequests.getResourceRequests();
        $('#' + formId + ' .resourceRequest').select2({
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
        $('#' + formId + ' .resourceRequest')
            .val(value)
            .trigger('change');
    }
}

const ResourceRequestSelect = new resourceRequestSelect();

export { ResourceRequestSelect as default };