// fetch request
const knownRfs = fetch("ajax/getKnownRfs.php")
    .then((response) =>
        response.json()
    )
    .catch((error) => {
        console.log(error);
    })
    .finally(() => {
        // console.log('Execute always ');
    });

export default await knownRfs;