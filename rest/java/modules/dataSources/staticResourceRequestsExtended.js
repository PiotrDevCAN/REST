/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticResourceRequestsExtended.js');

class staticResourceRequestsExtended {

    resourceRequests = [];

    constructor() {

    }

    async getResourceRequests() {
        // await for API data
        var dataRaw = await APIData.data;
        this.resourceRequests = dataRaw;
        return this.resourceRequests;
    }
}

const StaticResourceRequestsExtended = new staticResourceRequestsExtended();

export { StaticResourceRequestsExtended as default };
