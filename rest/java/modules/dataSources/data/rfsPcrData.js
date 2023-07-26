let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const rfsPcrData = async (rfsId, rfsPcrId) => {

    const url = 'ajax/getRfsPcrData.php';
    let params = {
        rfsId: rfsId,
        rfsPcrId: rfsPcrId
    };

    const data = await post(url, params);
    return data;
};

export default rfsPcrData;
