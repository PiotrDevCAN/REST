/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticOrganisations.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticOrganisationsIds {

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

const StaticOrganisationsIds = new staticOrganisationsIds();

export { StaticOrganisationsIds as default };
