/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/organisations.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class organisations {

    organisations = [];

    constructor() {

    }

    async getOrganisations() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.organisations = data;
        return this.organisations;
    }
}

const Organisations = new organisations();

export { Organisations as default };
