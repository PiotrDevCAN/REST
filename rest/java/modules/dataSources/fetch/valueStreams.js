// fetch request
const valueStreams = fetch("ajax/getValueStreams.php").then((response) => response.json());

export default await valueStreams;