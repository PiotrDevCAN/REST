/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/servicesByOrganisation.js');

class servicesByOrganisation {

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

const Services = new servicesByOrganisation();

export { Services as default };
