/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticResourceTypes.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticResourceTypes {

    resourceTypes = [];

    constructor() {

    }

    async getResourceTypes() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.resourceTypes = data;
        return this.resourceTypes;
    }
}

const StaticResourceTypes = new staticResourceTypes();

export { StaticResourceTypes as default };
