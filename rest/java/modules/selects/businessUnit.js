/**
 *
 */

let setSelect2 = await cacheBustImport('./modules/selects/setSelect2.js');
let BusinessUnits = await cacheBustImport('./modules/dataSources/businessUnits.js');
let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

class businessUnitSelect {

    id = 'selectBusinessUnit';
    cookieName = 'selectedBusinessUnit';

    constructor() {

    }

    async prepareDataForSelect() {
        var data = await BusinessUnits.getUnits();
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

const BusinessUnitSelect = new businessUnitSelect();
await BusinessUnitSelect.prepareDataForSelect();

export { BusinessUnitSelect as default };