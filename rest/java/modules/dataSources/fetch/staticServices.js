// fetch request
const staticServices = fetch("ajax/getStaticServices.php").then((response) => response.json());

export default await staticServices;