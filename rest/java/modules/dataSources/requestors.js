/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/requestors.js');
let mapper = await cacheBustImport('./modules/select2dataMapper.js');

class requestors {

    requestors = [];

    constructor() {

    }

    async getRequestors() {
        // await for API data
        var dataRaw = await APIData.data;
        var data = mapper(dataRaw);
        this.requestors = data;
        return this.requestors;
    }
}

const Requestors = new requestors();

export { Requestors as default };
