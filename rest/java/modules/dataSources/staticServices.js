/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticServices.js');

class staticServices {

    services = [];

    constructor() {

    }

    async getServices() {
        // await for API data
        var dataRaw = await APIData.data;
        this.services = dataRaw;
        return this.services;
    }
}

const StaticServices = new staticServices();

export { StaticServices as default };
