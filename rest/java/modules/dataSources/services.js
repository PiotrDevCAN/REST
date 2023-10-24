/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/services.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class services {

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

const Services = new services();

export { Services as default };
