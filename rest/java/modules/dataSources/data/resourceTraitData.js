let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const resourceTraitData = async (resourceTraitId) => {

    const url = 'ajax/getResourceTraitData.php';
    let params = {
        resourceTraitId: resourceTraitId
    };

    const data = await post(url, params);
    return data;
};

export default resourceTraitData;
