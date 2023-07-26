// fetch request
const staticSubcoNames = fetch("ajax/getStaticSubcoNames.php").then((response) => response.json());

export default await staticSubcoNames;