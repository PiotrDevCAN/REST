let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const vbacActiveResourcesData = async (fetchAll) => {

    const url = 'ajax/getVbacActiveResourcesForSelect2.php';
    let params = {
        fetchAll: fetchAll
    };

    const data = await post(url, params);
    return data.data;
};

export default vbacActiveResourcesData;