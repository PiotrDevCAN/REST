/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticResourceRequests.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticResourceRequests {

    resourceRequests = [];

    constructor() {

    }

    async getResourceRequests() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.resourceRequests = data;
        return this.resourceRequests;
    }
}

const StaticResourceRequests = new staticResourceRequests();

export { StaticResourceRequests as default };
