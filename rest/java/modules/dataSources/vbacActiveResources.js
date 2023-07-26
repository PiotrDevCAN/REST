/**
 *
 */

let APIData = await cacheBustImport('./modules/dataSources/fetch/vbacActiveResources.js');

class vbacActiveResources {

    activeResources = [];

    constructor() {

    }

    async getActiveResources() {
        // await for API data
        var dataRaw = await APIData.data;
        this.activeResources = dataRaw;
        return this.activeResources;
    }
}

const VBACActiveResources = new vbacActiveResources();

export { VBACActiveResources as default };
