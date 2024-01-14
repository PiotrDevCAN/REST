/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/businessUnits.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class businessUnitsIds {

    units = [];

    constructor() {

    }

    async getUnits() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.units = data;
        return this.units;
    }
}

const BusinessUnitsIds = new businessUnitsIds();

export { BusinessUnitsIds as default };
