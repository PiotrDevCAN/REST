// fetch request
const staticValueStreams = fetch("ajax/getStaticValueStreams.php").then((response) => response.json());

export default await staticValueStreams;