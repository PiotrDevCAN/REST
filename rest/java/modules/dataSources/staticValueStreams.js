/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticValueStreams.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class staticValueStreams {

    valueStreams = [];

    constructor() {

    }

    async getValueStreams() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.servvalueStreamsices = data;
        return this.servvalueStreamsices;
    }
}

const StaticValueStreams = new staticValueStreams();

export { StaticValueStreams as default };
