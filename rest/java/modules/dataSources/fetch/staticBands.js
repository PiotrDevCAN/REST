// fetch request
const staticBands = fetch("ajax/getStaticBands.php").then((response) => response.json());

export default await staticBands;