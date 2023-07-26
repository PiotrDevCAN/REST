/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/valueStreams.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class valueStreams {

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

const ValueStreams = new valueStreams();

export { ValueStreams as default };
