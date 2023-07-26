// fetch request
const staticPSBands = fetch("ajax/getStaticPSBands.php").then((response) => response.json());

export default await staticPSBands;