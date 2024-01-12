/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticBusinessUnits.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticBusinessUnits {

    businessUnits = [];

    constructor() {

    }

    async getBusinessUnits() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.businessUnits = data;
        return this.businessUnits;
    }
}

const StaticBusinessUnits = new staticBusinessUnits();

export { StaticBusinessUnits as default };
