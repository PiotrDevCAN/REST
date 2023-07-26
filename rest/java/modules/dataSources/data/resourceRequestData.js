let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const resourceRequestData = async (resourceReference) => {

    const url = 'ajax/getResourceRequestData.php';
    let params = {
        resourceReference: resourceReference
    };

    const data = await post(url, params);
    return data;
};

export default resourceRequestData;