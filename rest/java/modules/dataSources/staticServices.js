/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticServices.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class staticServices {

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

const StaticServices = new staticServices();

export { StaticServices as default };
