let post = await cacheBustImport('./modules/dataSources/fetch/post.js');
let mapper = await cacheBustImport('./modules/select2dataIdValueMapper.js');

const PSBandByResourceType = async (resourceTypeId) => {

    const url = 'ajax/getPSBandsByResourceType.php';
    let params = {
        resourceTypeId: resourceTypeId
    };

    const dataRawRaw = await post(url, params);
    const dataRaw = dataRawRaw.data;
    const data = mapper(dataRaw);
    return data;
};

export default PSBandByResourceType;
