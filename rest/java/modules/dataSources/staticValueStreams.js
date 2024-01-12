/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticValueStreams.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticValueStreams {

    valueStreams = [];

    constructor() {

    }

    async getValueStreams() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.valueStreams = data;
        return this.valueStreams;
    }
}

const StaticValueStreams = new staticValueStreams();

export { StaticValueStreams as default };
