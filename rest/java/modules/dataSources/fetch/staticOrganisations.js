// fetch request
const staticOrganisations = fetch("ajax/getStaticOrganisations.php").then((response) => response.json());

export default await staticOrganisations;