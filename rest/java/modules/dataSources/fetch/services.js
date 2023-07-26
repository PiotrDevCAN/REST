// fetch request
const services = fetch("ajax/getServices.php").then((response) => response.json());

export default await services;