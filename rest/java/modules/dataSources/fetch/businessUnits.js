// fetch request
const businessUnits = fetch("ajax/getBusinessUnits.php").then((response) => response.json());

export default await businessUnits;