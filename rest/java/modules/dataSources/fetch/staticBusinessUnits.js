// fetch request
const staticBusinessUnits = fetch("ajax/getStaticBusinessUnits.php").then((response) => response.json());

export default await staticBusinessUnits;