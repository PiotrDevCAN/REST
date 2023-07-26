/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/knownRfs.js');

class knownRfs {

    rfsList = [];

    constructor() {
        
    }

    async getRfses() {
        // await for API data
        var dataRaw = await APIData.data;
        this.rfsList = dataRaw;
        return this.rfsList;
    }

    addRef(newRfsId) {
        this.rfsList[newRfsId] = newRfsId;
    }
}

const KnownRfs = new knownRfs();

export { KnownRfs as default };
