/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticPSBands.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticPSBands {

    PSBands = [];

    constructor() {

    }

    async getPSBands() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.PSBands = data;
        return this.PSBands;
    }
}

const StaticPSBands = new staticPSBands();

export { StaticPSBands as default };
