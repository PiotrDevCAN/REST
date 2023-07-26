// fetch request
const services = fetch("ajax/getServicesByOrganisation.php").then((response) => response.json());

export default await services;