window.WAPP.getOrAddChat = function(id)
{

    vchatWid = window.Store.WidFactory.createWid(id);

    var chat = Store.Chat.gadd(vchatWid);
    /*gambiarra correção*/
    chat.sendMessage = (chat.sendMessage) ? chat.sendMessage : function () { return window.Store.sendMessage.apply(this, arguments); };
    /*------------------*/
    return chat;
}

window.WAPI.base64ImageToFile = function (b64Data, filename) {
    var arr = b64Data.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, {type: mime});
};


window.WAPP.sendText = function(targetId, message, done)
{
    try
    {
        var chat = window.WAPP.getOrAddChat( targetId ); //Store.Chat.gadd(contact.id);
        chat.sendMessage(message);
        //chat.sendSeen(false);
    }
    catch(err)
    {
        console.log('WAPP.sendText error: '+err.message);
    }

    if (typeof done === "function")
    {
        done(true);
    }
}

window.WAPP.sendFile = function(targetId, imgBase64, options, done)
{
    var idUser = new Store.WidFactory.createWid(targetId, {
        intentionallyUsePrivateConstructor: true
    });

    var chat = window.WAPP.getOrAddChat(targetId);
    
    var mediaBlob = window.WAPI.base64ImageToFile(imgBase64, options.filename);
    var mc = new Store.MediaCollection(chat);

    mc.processAttachments([{file: mediaBlob}, 1], 1, chat).then(() => {
        let media = mc._models[0];
        media.sendToChat(chat, options);
        return true;
    });
}




// https://github.com/wppconnect-team/wa-js
// https://github.com/wppconnect-team/wa-js/releases
// https://github.com/wppconnect-team/wa-js/releases/download/nightly/wppconnect-wa.js


batchList = [];
function sendBatchFake(send) 
{

    send = Object.assign(
        send,
        {
            jid: '55555555',
            status: 200
        }
    );

    batchList.push(send); 
    console.log(JSON.stringify(send));

    //batch = null;
    let msgLength = batchList.length;

    if (msgLength > 25) 
    {
       // batchList.splice(msgLength-25, 1);               
    }    


}


function releaseMsg()
{
    let msgLength = Store.Msg._models.length;

    if (msgLength > 25) 
    {
        Store.Msg._models[msgLength-25].delete();               
    }    

    //return msgLength-1;
}


window.WAPP.sendBatch = function(send, done)
{ 

    targetIdWithAt = 
        send.batch.target.address + '@c.us';

    WAPP.checkNumberStatus(targetIdWithAt, function(contact)
    { 
        send = Object.assign(
            send,
            {
                href: '/send',
                jid: contact.jid,
                status: contact.status
            }
        );

        if (contact.status == 200)
        {
            
            send.batch.messages.forEach((msg, msgIndex) => {

                console.log('delay to send: ' + (msgIndex * 1000));

                setTimeout(() => {

                    if ((msg.text) && (msg.files.length == 0)) {
                        console.log('TEXT,,,');

                        //var messg = Base64.decode(msg.text.body);       
                        var messg = decodeURIComponent(msg.text.body);       

                        var regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/gi;
                        var links = messg.match(regex);

                        //setTimeout(function() {
                        //if (links.length > 0)
                        //    WAPP.sendLinkPreview(send.jid, links[0], messg); else 
                            WAPP.sendText(send.jid, messg); 
                        //}, aindex * 1000);
                    }

                    if ((msg.text) && (msg.files.length >= 1)) {
                        
                        file = msg.files[0];

                        //console.log('FILE,,,');
                        if (file.fileType == 'IMAGE') 
                            fileCaption = decodeURIComponent(file.caption); else
                        if (file.fileType == 'VIDEO') 
                            fileCaption = decodeURIComponent(file.caption); else
                            fileCaption = file.fileName;    
                            
                        fileName    = file.fileName; 
                        fileCaption = decodeURIComponent(file.caption);   
                        fileBase64  = file.fileData;    

                        let fileOptions = {
                            type: 'auto-detect', 
                            isPtt: true, 
                            filename: fileName, 
                            caption: fileCaption
                        };

                        if (file.fileType == 'AUDIO')
                            Object.assign(fileOptions, {isPtt: true, type: 'audio'});


                        if (file.fileType == 'VCARD') 
                            WAPP.sendCard(send.jid, fileBase64); else
                            WAPP.sendFile(send.jid, fileBase64, fileOptions);

                    }

                    releaseMsg();

                    
                }, msgIndex * 250);
            });


            
        
        }

        setTimeout(function() {

            console.log(JSON.stringify(
                send
            ));

        }, 100);
    });
}

console.log('---=> 50_batch.js');