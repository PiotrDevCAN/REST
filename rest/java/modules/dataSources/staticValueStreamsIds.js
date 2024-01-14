/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/staticValueStreams.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

class staticValueStreamsIds {

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

const StaticValueStreamsIds = new staticValueStreamsIds();

export { StaticValueStreamsIds as default };
