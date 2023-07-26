/**
 *
 */

class rfsPcrIdValidator {

    constructor() {
        console.log('+++ Function +++ rfsPcrIdValidator.constructor');

        console.log('--- Function --- rfsPcrIdValidator.constructor');
    }

    clearError(el) {
        helper.offHighlight(el);
        helper.unlockSubmitButton();
        $('#rfsPcrIdInvalid').hide();
    }

    flagError(el) {
        helper.highlightOnRed(el);
        helper.lockSubmitButton();
        $('#rfsPcrIdInvalid').show();
    }

    checkIdMeetsPattern(el) {
        var pcrNumber = helper.getId(el);
        if (pcrNumber !== '') {
            var isValid = helper.checkIfRfsIsValid(pcrNumber);
            if (isValid) {
                this.clearError(el);
                return true;
            } else {
                this.flagError(el);
                return false;
            }
        } else {
            this.clearError(el);
            return true;
        }
    }

    async preventDuplicateEntry(el) {
        var pcrNumber = helper.getId(el);
        if (pcrNumber !== '') {
            var newPcrNumber = pcrNumber.toUpperCase().replace(/_|\s/g, '-');
            if (pcrNumber !== newPcrNumber) {
                $(el).val(newPcrNumber);
            } else {

            }
            var allreadyExists = await helper.checkIfRfsPcrIdExists(newPcrNumber);
            if (allreadyExists) { // comes back with Position in array(true) or false is it's NOT in the array.
                helper.highlightOnRed(el);
                helper.lockSubmitButton();
                helper.displayMessageModal("<h4>PCR Number: " + newPcrNumber + " you have specified has already been defined, please enter a unique PCR Number</h4>");
                return false;
            } else {
                helper.highlightOnGreen(el);
                helper.unlockSubmitButton();
                return true;
            }
        } else {
            helper.offHighlight(el);
            helper.unlockSubmitButton();
            return false;
        }
    }

    async validateId(el) {
        var patternMet = this.checkIdMeetsPattern(el);	// 31
        if (patternMet) {
            var notDuplicate = await this.preventDuplicateEntry(el);	// 51
            if (notDuplicate) {

            }
        }
    }
}

const RfsPcrIdValidator = new rfsPcrIdValidator();

export { RfsPcrIdValidator as default };
