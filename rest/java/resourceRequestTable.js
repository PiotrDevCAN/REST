/**
 *
 */


function ResourceRequestTable() {

	this.init = function(){
		console.log('+++ Function +++ ResourceRequestTable.init');
		console.log('--- Function --- ResourceRequestTable.init');

	}


}


$( document ).ready(function() {
	var resourceRequestTable = new ResourceRequestTable();
    resourceRequestTable.init();
});