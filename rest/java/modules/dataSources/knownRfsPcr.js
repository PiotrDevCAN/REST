/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/knownRfsPcr.js');

class knownRfsPcr {

    rfsPcrList = [];

    constructor() {
        
    }

    async getRfsPcrs() {
        // await for API data
        var dataRaw = await APIData.data;
        this.rfsPcrList = dataRaw;
        return this.rfsPcrList;
    }

    addRef(newRfsPcrId) {
        this.rfsPcrList[newRfsPcrId] = newRfsPcrId;
    }
}

const KnownRfsPcr = new knownRfsPcr();

export { KnownRfsPcr as default };
