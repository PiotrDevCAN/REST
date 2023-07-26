function formatResourceRequest(resource) {
	var rfsId = resource.rfsId;
	var resourceReference = resource.resourceReference;
	var resourceName = 'Resource is unallocated';
	if (resource.resourceName !== '') {
		resourceName = resource.resourceName;
	}
	var text = "<span style='color:black'><b>&nbsp;" + resourceReference + "</b></span>";
	text += "<br/>&nbsp;&nbsp;" + resourceName;
	text += "<br/>&nbsp;&nbsp;";
	text += "<span style='color:silver'>" + rfsId + "<span>";
	var textObj = $(text);
	return textObj;
}

export { formatResourceRequest as default };