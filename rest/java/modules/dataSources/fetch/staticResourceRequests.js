// fetch request
const staticResourceRequests = fetch("ajax/getResourceRequests.php").then((response) => response.json());

export default await staticResourceRequests;