let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const bespokeRateData = async (bespokeRateId) => {

    const url = 'ajax/getBespokeRateData.php';
    let params = {
        bespokeRateId: bespokeRateId
    };

    const data = await post(url, params);
    return data;
};

export default bespokeRateData;
