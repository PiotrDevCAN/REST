let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const rateCardData = async (resourceName) => {

    const url = 'ajax/getRateCardData.php';
    let params = {
        resourceName: resourceName
    };

    const data = await post(url, params);
    return data;
};

export default rateCardData;