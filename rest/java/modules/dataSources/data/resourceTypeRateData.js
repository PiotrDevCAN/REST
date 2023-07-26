let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const resourceTypeRateData = async (resourceTypeId, psBandId) => {

    const url = 'ajax/getResourceTypeRateData.php';
    let params = {
        resourceTypeId: resourceTypeId,
        psBandId: psBandId
    };

    const data = await post(url, params);
    return data;
};

export default resourceTypeRateData;
