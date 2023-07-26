// fetch request
const staticResourceTypes = fetch("ajax/getStaticResourceTypes.php").then((response) => response.json());

export default await staticResourceTypes;