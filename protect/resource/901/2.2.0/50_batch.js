window.WAPP.getOrAddChat = function(id)
{

    vchatWid = window.Store.WidFactory.createWid(id);

    var chat = Store.Chat.gadd(vchatWid);
    /*gambiarra correção*/
    chat.sendMessage = (chat.sendMessage) ? chat.sendMessage : function () { return window.Store.sendMessage.apply(this, arguments); };
    /*------------------*/
    return chat;
}
/*
window.WAPP.getOrAddChat = function (id) {
    id = typeof id == "string" ? id : id._serialized;
    vchatWid = window.Store.WidFactory.createWid(id);

    const found = window.Store.Chat.get(vchatWid);
    if (found) found.sendMessage = (found.sendMessage) ? found.sendMessage : function () { return window.Store.sendMessage.apply(this, arguments); };
    return found;
}
*/
window.WAPP.sendText = function(contact, message, done)
{
    try
    {
        
        var chat = window.WAPP.getOrAddChat(contact.jid); //Store.Chat.gadd(contact.id);
        chat.sendMessage(message);
        //chat.sendSeen(false);
    }
    catch(err)
    {
        console.log('WAPP.sendText error: '+err.message);
    }

    if (typeof done === "function")
    {
        done(contact);
    }
}


window.WAPP.sendLinkWithPreview = async function (contact, text, url) {

    var chat = window.WAPP.getOrAddChat(contact.jid); //Store.Chat.gadd(contact.id);

    const linkPreview = await Store.WapQuery.queryLinkPreview(url);
    return (await chat.sendMessage(text.includes(url) ? text : `${url}\n${text}`, {linkPreview}))=='success'
}

/*
window.WAPI.sendLinkWithAutoPreview = async function (chatId, url, text) {
    var chatSend = WAPI.getChat(chatId);
    if (chatSend === undefined) {
        return false;
    }
    const linkPreview = await Store.WapQuery.queryLinkPreview(url);
    return (await chatSend.sendMessage(text.includes(url) ? text : `${url}\n${text}`, {linkPreview}))=='success'
}
*/

window.WAPP.sendFile070622 = function(contact, imgBase64, caption, done)
{
    var chat      = window.WAPP.getOrAddChat(contact.jid); //Store.Chat.gadd(contact.id);
    var mediaBlob = window.WAPI.base64ImageToFile(imgBase64, /*null*/ caption);
    var mc        = new Store.MediaCollection(chat);

    mc.processAttachments([{file: mediaBlob}, 1], chat, 1).then(() => {
        let media = mc.models[0];
        media.sendToChat(chat, {caption:caption});
        if (done !== undefined) done(true);
    });
}

window.WAPP.sendFile = function(contact, imgBase64, caption, done)
{
    console.log('will idUser');
    var idUser = new Store.WidFactory.createWid(contact.jid, {
        intentionallyUsePrivateConstructor: true
    });
    console.log('idUser!');
    
    
        //Store.FindChat.findChat(idUser).then((chat) => {
        //    console.log('entro chat: ', chat)
          
          var chat      = window.WAPP.getOrAddChat(contact.jid);  
            var mediaBlob = window.WAPI.base64ImageToFile(imgBase64, caption);
            var mc = new Store.MediaCollection(chat);
            
    
            //New - Mike Lustosa 07/06/2022
            mc.processAttachments([{file: mediaBlob}, 1], chat, 1).then(() => {
                let media = mc._models[0];
                media.sendToChat(chat, {caption:caption});
                if (done !== undefined) done(true);
            });
        //});
    
}






window.WAPI.add9 = function(jid) {
    if ((jid.substr(0,2) == "55") && (jid.length == 17)) {
        console.log('need add 9');
        return jid.substr(0,4) + "9" + jid.substr(4,100)        
    } else {
        return jid;
    }
}
window.WAPI.del9 = function(jid) {
    if ((jid.substr(0,2) == "55") && (jid.length == 18)) {
        console.log('need del 9');
        return jid.substr(0,4) + jid.substr(5,100)        
    } else {
        return jid;
    }
}

window.WAPI.normalize9 = function(jid) {
    //console.log(parseInt(jid.substr(2,2));
    return (parseInt(jid.substr(2,2)) > 30) ? WAPI.del9(jid) : WAPI.add9(jid);
}

window.WAPI.checkNumberStatus2 = function(fid, done) {

    var contact = WAPP.getOrAddChat( WAPI.del9( fid ) );

    if (contact == undefined)
    {
        console.log(WAPI.del9( fid ) + ' not found, trying ' + WAPI.add9( fid ));

        contact = WAPP.getOrAddChat( WAPI.add9( fid ) );
    }


    if (contact == undefined){

        contact =
        {                    
            status: 404,
            isBusiness: false,
            canReceiveMessage: true,
            id: {_serialized: fid}
        }    
    } else {
        
        contact = Object.assign(
                    contact,
                    {
                        //jid: contact.id._serialized || undefined,
                        status: 200,
                        isBusiness: false,
                        canReceiveMessage: true,
                    }
                );
    }

    done(contact);

}


window.WAPP.checkNumberStatusFake = function(fid, done) {
    done({
        jid: WAPI.normalize9(fid) || undefined,
        status: 200,
        isBusiness: false,
        canReceiveMessage: true,
    })
}

window.WAPP.sendBatch = function(batch, done)
{
    // console.log('111111111111111111111111111111111111111111111111111111111111111111111111');
    // console.log(JSON.stringify(batch));
    // console.log('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
    if (!!batch.messages.listHelper)
    {
        console.log("has items");
        if (batch.messages.listHelper.slice(-1)[0] == null)
        {
            console.log("slice is null");
            batch.messages.listHelper.pop();
        } else console.log("slice is NOT null");
    } else console.log("NOT has items");

    // console.log('222222222222222222222222222222222222222222222222222222222222222222222222');
    // console.log(JSON.stringify(batch));
    // console.log('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');

    targetIdWithAt = 
        batch.target.id + '@c.us';

        //console.log(JSON.stringify(batch));

    //WAPP.checkNumberStatusFake(targetIdWithAt, function(contact)
    WAPP.checkNumberStatus(targetIdWithAt, function(contact)
    {

        //console.log(JSON.stringify(
        //        batch
        //    ));

        if (contact.status == 500)
        {
        //    contact.status = 404;
        }

        if (!contact.canReceiveMessage)
        {
            contact.jid = targetIdWithAt;
            contact.status = 404;
        }


            batch = Object.assign(
                {
                    href: '/batch',
                },
                batch
            );

            batch = Object.assign(
                batch,
                {
                    jid: contact.jid || undefined,
                    status: contact.status,
                    isBusiness: contact.isBusiness,
                    canReceiveMessage: contact.canReceiveMessage,
                }
            );

            if (contact.status == 200)
            {
                if (!!batch.messages.listHelper)
                {
                    batch.messages.listHelper.forEach((msg) => {
                        //console.log(111);
                        if (msg.text) {

                            var messg = Base64.decode(msg.text.body);       
                            var regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/gi;
                            var links = messg.match(regex);

                            if ((!links) || (WAPP.__isBeta()))
                                WAPP.sendText(contact, messg); else
                            {
                                console.log(links[0]);
                                WAPP.sendLinkWithPreview(contact, messg, links[0]);
                            }

                        } else
                        if (msg.file) {
                            
                            if (msg.file.fileType == 'IMAGE') 
                                fileCaption = Base64.decode(msg.file.caption); else
                                fileCaption = msg.file.fileName;    
                            
                            WAPP.sendFile(contact, msg.file.base64, fileCaption);
                        }
                    });
                }
            }

            if (!!batch.messages.listHelper)
            {
                batch.messages.listHelper.forEach((msg) => {
                         try {
                             //console.log(222);
                             if (msg.text) {
                                 //msg.text.body  = 'body';
                             } else
                             if (msg.file) {
                                 msg.file.base64  = 'base64 cleaned!';
                                 msg.file.caption = 'caption';
                             }
                         }
                         catch(err) {
                             console.log(err.message);
                         }
                });
            }

            setTimeout(function() {

                console.log(JSON.stringify(
                    batch
                ));

            }, 100);
    });
}

console.log('---=> 50_batch.js caruau checkNumberStatusFake AGAIN 2.1.3');