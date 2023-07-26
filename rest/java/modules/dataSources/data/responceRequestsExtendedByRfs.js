let post = await cacheBustImport('./modules/dataSources/fetch/post.js');
let mapper = await cacheBustImport('./modules/select2dataRRMapper.js');

const responceRequestsExtendedByRfs = async (rfsId) => {

    const url = 'ajax/getResourceRequestsExtendedByRfs.php';
    let params = {
        rfsId: rfsId
    };

    const dataRawRaw = await post(url, params);
    const dataRaw = dataRawRaw.data;
    const data2 = dataRaw[rfsId];
    const data = mapper(data2);
    return data;
};

export default responceRequestsExtendedByRfs;
