let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const resourceRateData = async (resourceRateId) => {

    const url = 'ajax/getResourceRateData.php';
    let params = {
        resourceRateId: resourceRateId
    };

    const data = await post(url, params);
    return data;
};

export default resourceRateData;
