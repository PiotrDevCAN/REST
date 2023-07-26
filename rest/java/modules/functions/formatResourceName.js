function formatResourceName(resource) {
	var symbol = resource.distance == 'local' ? '' : '';

	// resource.emailAddress
	// resource.kynEmailAddress

	var text = $("<span style='color:black' >&nbsp;" + resource.text + "</span><br/>&nbsp;&nbsp;" + resource.role + "<br/>&nbsp;&nbsp;<span style='color:silver'>" + resource.tribe + "<span>");
	return text;
}

export { formatResourceName as default };