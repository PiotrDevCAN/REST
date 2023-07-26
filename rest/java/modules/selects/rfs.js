/**
 *
 */

let setSelect2 = await cacheBustImport('./modules/selects/setSelect2.js');
let RFSs = await cacheBustImport('./modules/dataSources/staticRFSs.js');
let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

class refSelect {

    id = 'selectRfs';
    cookieName = 'selectedRfs';

    constructor() {

    }

    async prepareDataForSelect() {
        var data = await RFSs.getRFSs();
        setSelect2(this.id, this.cookieName, data);
    }

    listenForSelectChange(table) {
        var $this = this;
        $(document).on('change', '#' + this.id, function () {
            var value = $('#' + this.id + ' option:selected').val();
            // document.cookie = $this.cookieName+"=" + org + ";" + "path=/;max-age=604800;samesite=lax;";
            setCookie($this.cookieName, value, 7);
            table.ajax.reload();
        });
    }
}

const RefSelect = new refSelect();
await RefSelect.prepareDataForSelect();

export { RefSelect as default };