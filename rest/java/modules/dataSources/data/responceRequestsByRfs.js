let post = await cacheBustImport('./modules/dataSources/fetch/post.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

const responceRequestsByRfs = async (rfsId) => {

    const url = 'ajax/getResourceRequestsByRfs.php';
    let params = {
        rfsId: rfsId
    };

    const dataRawRaw = await post(url, params);
    const dataRaw = dataRawRaw.data;
    const data = mapper(dataRaw);
    return data;
};

export default responceRequestsByRfs;
