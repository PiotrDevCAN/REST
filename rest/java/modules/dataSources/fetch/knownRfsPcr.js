// fetch request
const knownRfsPcr = fetch("ajax/getKnownRfsPcr.php")
    .then((response) =>
        response.json()
    )
    .catch((error) => {
        console.log(error);
    })
    .finally(() => {
        // console.log('Execute always ');
    });

export default await knownRfsPcr;