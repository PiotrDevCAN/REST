/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticRFSs.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticRFSs {

    RFSs = [];

    constructor() {

    }

    async getRFSs() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.RFSs = data;
        return this.RFSs;
    }
}

const StaticRFSs = new staticRFSs();

export { StaticRFSs as default };
