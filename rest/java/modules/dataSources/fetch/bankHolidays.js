// fetch request
const holidays = fetch("https://www.gov.uk/bank-holidays.json").then((response) => response.json());

export default await holidays;