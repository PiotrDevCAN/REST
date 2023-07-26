/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticOrganisations.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class staticOrganisations {

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

const StaticOrganisations = new staticOrganisations();

export { StaticOrganisations as default };
