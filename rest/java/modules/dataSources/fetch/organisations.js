// fetch request
const organisations = fetch("ajax/getOrganisations.php").then((response) => response.json());

export default await organisations;