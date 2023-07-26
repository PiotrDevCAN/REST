// fetch request
const staticResourceRequestsExtended = fetch("ajax/getResourceRequestsExtended.php").then((response) => response.json());

export default await staticResourceRequestsExtended;