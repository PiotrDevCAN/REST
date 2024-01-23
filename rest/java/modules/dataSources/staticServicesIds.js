/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticServices.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticServicesIds {

    services = [];

    constructor() {

    }

    async getServices() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.services = data;
        return this.services;
    }
}

const StaticServices = new staticServicesIds();

export { StaticServices as default };
