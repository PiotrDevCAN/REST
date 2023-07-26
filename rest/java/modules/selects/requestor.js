/**
 *
 */

let setSelect2 = await cacheBustImport('./modules/selects/setSelect2.js');
let Requestors = await cacheBustImport('./modules/dataSources/requestors.js');
let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

class requestoSelect {

    id = 'selectRequestor';
    cookieName = 'selectedRequestor';

    constructor() {

    }

    async prepareDataForSelect() {
        var data = await Requestors.getRequestors();
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

const RequestoSelect = new requestoSelect();
await RequestoSelect.prepareDataForSelect();

export { RequestoSelect as default };