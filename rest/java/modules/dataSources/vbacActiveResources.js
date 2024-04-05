/**
 *
 */

let vbacActiveResourcesData = await cacheBustImport('./modules/dataSources/data/vbacActiveResourcesData.js');

class vbacActiveResources {

    activeResources = [];
    allActiveResources = [];

    constructor() {

    }

    async getActiveResources() {
        // await for API data
        if (this.activeResources.length == 0) {
            var dataRaw = await vbacActiveResourcesData();
            this.activeResources = dataRaw;
        }
        return this.activeResources;
    }

    async getAllActiveResources() {
        // await for API data
        if (this.allActiveResources.length == 0) {
            var dataRaw = await vbacActiveResourcesData(true);
            this.allActiveResources = dataRaw;
        }
        return this.allActiveResources;
    }
}

const VBACActiveResources = new vbacActiveResources();

export { VBACActiveResources as default };
