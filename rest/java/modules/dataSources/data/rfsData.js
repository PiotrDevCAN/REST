let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const rfsData = async (rfsId) => {

    const url = 'ajax/getRfsData.php';
    let params = {
        rfsId: rfsId
    };

    const data = await post(url, params);
    return data;
};

export default rfsData;