// fetch request
const requestors = fetch("ajax/getRequestors.php").then((response) => response.json());

export default await requestors;