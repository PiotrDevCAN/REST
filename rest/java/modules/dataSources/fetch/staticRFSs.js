// fetch request
const getRFSs = fetch("ajax/getRfses.php").then((response) => response.json());

export default await getRFSs;