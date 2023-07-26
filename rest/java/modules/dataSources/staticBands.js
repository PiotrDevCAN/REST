/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticBands.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticBands {

    bands = [];

    constructor() {

    }

    async getBands() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.bands = data;
        return this.bands;
    }
}

const StaticBands = new staticBands();

export { StaticBands as default };
