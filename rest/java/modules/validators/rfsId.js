/**
 *
 */

class rfsIdValidator {

    constructor() {
        console.log('+++ Function +++ rfsIdValidator.constructor');

        console.log('--- Function --- rfsIdValidator.constructor');
    }

    clearError(el) {
        helper.offHighlight(el);
        helper.unlockSubmitButton();
        $('#rfsIdInvalid').hide();
    }

    flagError(el) {
        helper.highlightOnRed(el);
        helper.lockSubmitButton();
        $('#rfsIdInvalid').show();
    }

    checkIdMeetsPattern(el) {
        var rfsId = helper.getId(el);
        if (rfsId !== '') {
            var isRfsId = helper.checkIfRfsIsValid(rfsId);
            if (isRfsId) {
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
        var rfsId = helper.getId(el);
        if (rfsId !== '') {
            var newRfsId = rfsId.toUpperCase().replace(/_|\s/g, '-');
            if (rfsId !== newRfsId) {
                $(el).val(newRfsId);
            } else {

            }
            var allreadyExists = await helper.checkIfRfsIdExists(newRfsId);
            if (allreadyExists) { // comes back with Position in array(true) or false is it's NOT in the array.
                helper.highlightOnRed(el);
                helper.lockSubmitButton();
                helper.displayMessageModal("<h4>RFS: " + newRfsId + " you have specified has already been defined, please enter a unique RFS ID</h4>");
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

    async checkOriginalEntryExists(el) {
        var rfsId = helper.getId(el);
        if (rfsId !== '') {
            var isInType = helper.checkIfRfsIsSpecificType(rfsId, 'PLD');
            if (isInType) {
                console.log('-PLD- id entered, check if original record exists');

                var originalRfsId = rfsId.toUpperCase().replace(/-PLD-|\s/g, '-RFS-');
                var allreadyExists = await helper.checkIfRfsIdExists(originalRfsId);
                if (allreadyExists) {
                    console.log(originalRfsId);
                    console.log('original record exists');

                    console.log('read rfs end date from database');

                    var $this = this;
                    var maxEndDate = null;
                    $.ajax({
                        url: "ajax/endDateForRfs.php",
                        type: 'POST',
                        data: {
                            rfs: originalRfsId
                        },
                        success: function (result) {
                            try {
                                var resultObj = JSON.parse(result);
                                if (resultObj.rfsEndDate !== null) {
                                    alert('end date of original record ' + resultObj.rfsEndDate);

                                    var originalStartDateStr = resultObj.rfsEndDate;
                                    var date = new Date(originalStartDateStr);
                                    date.setDate(date.getDate() + 1);
                                    alert('end date of original record ' + date);

                                    /*
                                    maxEndDate = new Date(resultObj.rfsEndDate);
                                    $this.endPicker.setDate(maxEndDate);
                                    $this.endPicker.setMaxDate(maxEndDate);
                                    $this.startPicker.setMaxDate(maxEndDate);
                                    */
                                }
                            } catch (e) {
                                helper.unlockButton();
                                helper.displayTellDevMessageModal(e);
                            }
                        }
                    });

                    helper.highlightOnGreen(el);
                    helper.unlockSubmitButton();
                    return true;
                } else {
                    helper.highlightOnRed(el);
                    helper.lockSubmitButton();
                    $('#messageBody').html("<h4>RFS: " + originalRfsId + " does not exist, <br>please create an original RFS first and then make an extention for that entry</h4>");
                    $('#messageModal').modal('show');
                    return false;
                }

            } else {
                console.log('-PLD- id not entered');
                return true;
            }
        } else {
            console.log('empty rfs id');
            return true;
        }
    }

    async validateId(el) {
        var patternMet = this.checkIdMeetsPattern(el);	// 31
        if (patternMet) {
            var notDuplicate = await this.preventDuplicateEntry(el);	// 51
            if (notDuplicate) {
                var hasOriginalEntry = await this.checkOriginalEntryExists(el); // 79
                if (hasOriginalEntry) {

                }
            }
        }
    }
}

const RfsIdValidator = new rfsIdValidator();

export { RfsIdValidator as default };
