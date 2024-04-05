function formatResourceName(resource) {
	var symbol = resource.distance == 'local' ? '' : '';
	var cssClass = '';
	switch (resource.disabled) {
		case false:
			cssClass = 'text-success';
			break;
		case true:
			cssClass = 'text-warning';
			break;
		default:
			break;
	}
	var text = $("<span class='" + cssClass + "'>&nbsp;(<b>" + resource.status + "</b>) " + resource.text + "</span>"
		+ "<br/>&nbsp;&nbsp;" + resource.role
		+ "<br/>&nbsp;&nbsp;<span style='color:silver'>" + resource.tribe + "<span>"
		+ "<br/>&nbsp;&nbsp;<span style='color:silver'>Assignment Type: " + resource.type + "<span>"
	);
	return text;
}

export { formatResourceName as default };