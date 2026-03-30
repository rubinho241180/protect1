window.WAPP.getOrAddChat = function(id)
{
    var chat = Store.Chat.gadd(id);
    /*gambiarra correção*/
    chat.sendMessage = (chat.sendMessage) ? chat.sendMessage : function () { return window.Store.sendMessage.apply(this, arguments); };
    /*------------------*/
    return chat;
}

window.WAPP.sendText = function(contact, message, done)
{
    try
    {
        
        var chat = window.WAPP.getOrAddChat(contact.id); //Store.Chat.gadd(contact.id);
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

window.WAPP.sendFile = function(contact, imgBase64, caption, done)
{
    var chat      = window.WAPP.getOrAddChat(contact.id); //Store.Chat.gadd(contact.id);
    var mediaBlob = window.WAPI.base64ImageToFile(imgBase64, /*null*/ caption);
    var mc        = new Store.MediaCollection(chat);

    /* old
    mc.processFiles([mediaBlob], chat, 1).then(() => {
        var media = mc.models[0];
        media.sendToChat(chat, {caption: caption});
        done(true);
    });
    */
    mc.processAttachments([{file: mediaBlob}, 1], chat, 1).then(() => {
        let media = mc.models[0];
        media.sendToChat(chat, {caption:caption});
        if (done !== undefined) done(true);
    });
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
 
    WAPI.checkNumberStatus(batch.target.id, function(contact)
    {
        nnn = Math.random();

        console.log('contact: '+nnn, JSON.stringify(
                contact
            ));
        //console.log(123);

        if (contact.status == 500)
        {
            contact.status = 404;
        }

        if (!contact.canReceiveMessage)
        {
            contact.jid = batch.target.id;
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
                    jid: contact.id._serialized || undefined,
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
                            WAPP.sendText(contact, Base64.decode(msg.text.body));
                        } else
                        if (msg.file) {
                            WAPP.sendFile(contact, msg.file.base64, Base64.decode(msg.file.caption));
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

            console.log(JSON.stringify(
                batch
            ));
    });
}

console.log('---=> 50_batch.js');