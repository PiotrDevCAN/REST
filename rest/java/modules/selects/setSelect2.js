/**
 *
 */

let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

function setSelect2(id, cookieName, data) {
    var obj = $('#'+id);
    obj.select2({
        data: data
    });

    var preSeletedVal = getCookie(cookieName);
    if (preSeletedVal !== '') {
        obj.val(preSeletedVal);
        obj.trigger('change');
    }
}

export { setSelect2 as default };