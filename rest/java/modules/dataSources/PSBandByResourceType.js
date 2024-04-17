/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/PSBandByResourceType.js');

class PSBandByResourceType {

    PSBands = [];

    constructor() {

    }

    async getPSBands(resourceTypeId) {
        // await for API data
        var dataRaw = await APIData.data;
        this.PSBands = dataRaw;
        return this.PSBands;
    }
}

const PsBandByResourceType = new PSBandByResourceType();

export { PsBandByResourceType as default };
