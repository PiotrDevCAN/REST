/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/PSBandByResourceType.js');
// let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class PSBandByResourceType {

    PSBands = [];

    constructor() {

    }

    async getPSBands(resourceTypeId) {
        // await for API data
        var dataRaw = await APIData.data;
        // var data = mapper(dataRaw);
        this.PSBands = dataRaw;
        return this.PSBands;
    }
}

const PsBandByResourceType = new PSBandByResourceType();

export { PsBandByResourceType as default };
