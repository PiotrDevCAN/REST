// fetch request
const vbacActiveResources = fetch("ajax/getVbacActiveResourcesForSelect2.php").then((response) => response.json());

export default await vbacActiveResources;