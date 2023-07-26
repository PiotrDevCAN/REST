/**
 *
 */

let setSelect2 = await cacheBustImport('./modules/selects/setSelect2.js');
let Organisations = await cacheBustImport('./modules/dataSources/organisations.js');
let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

class organisationSelect {

    id = 'selectOrganisation';
    cookieName = 'selectedOrganisation';

    constructor() {

    }

    async prepareDataForSelect() {
        var data = await Organisations.getOrganisations();
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

const OrganisationSelect = new organisationSelect();
await OrganisationSelect.prepareDataForSelect();

export { OrganisationSelect as default };
