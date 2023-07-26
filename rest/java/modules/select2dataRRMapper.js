/**
 *
 */

function map(data) {
	const mappedData = $.map(data, function (val, index) {
		return {
			id: val.RESOURCE_REFERENCE,
			text: val.RESOURCE_REFERENCE,
			rfsId: val.RFS,
			resourceReference: val.RESOURCE_REFERENCE,
			resourceName: val.RESOURCE_NAME
		};
	});
	return mappedData;
}

export { map as default };