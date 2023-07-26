/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticSubcoNames.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticSubcoNames {

    subcoNames = [];

    constructor() {

    }

    async getSubcoNames() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.subcoNames = data;
        return this.subcoNames;
    }
}

const StaticSubcoNames = new staticSubcoNames();

export { StaticSubcoNames as default };
