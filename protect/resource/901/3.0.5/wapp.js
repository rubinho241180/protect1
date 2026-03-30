WAPP = {};

WAPP.myCheckNumberVENOM = async function(fid, done) {

    console.log('myCheckNumberWPP...');
    
    await Store.WapQuery.queryPhoneExists(fid).then(result => {

        if (!!result)
        {
            done({
                status: 200,
                id : result.wid.user,
                jid: result.wid._serialized,
                isBusiness: false,
                canReceiveMessage: true
            });
        } else {
            done({
                status: 404,
                canReceiveMessage: false
            });
        }
    });
}

WAPP.checkNumberStatus = function(id, done) {
    
    WAPP.myCheckNumberVENOM( HELPERS.normalizePlus( id ), done );
}


