/**
 *
 */

let setSelect2 = await cacheBustImport('./modules/selects/setSelect2.js');
let ValueStreams = await cacheBustImport('./modules/dataSources/valueStreams.js');
let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

class valueStreamSelect {

    id = 'selectValueStream';
    cookieName = 'selectedValueStream';

    constructor() {

    }

    async prepareDataForSelect() {
        var data = await ValueStreams.getValueStreams();
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

const ValueStreamSelect = new valueStreamSelect();
await ValueStreamSelect.prepareDataForSelect();

export { ValueStreamSelect as default };