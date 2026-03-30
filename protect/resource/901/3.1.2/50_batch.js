createChatOption = {createChat: true};

WAPP.checkNumberStatus = function(targetId, done) {

    WPP.contact.queryExists(targetId).then((result) => {
        if (done !== undefined)
            done({
                id: !!result ? result.wid : null,
                status: !!result ? 200 : 404
            })
    })
}

WAPP.sendText = function(targetId, message, done) {

    WPP.chat.sendTextMessage(targetId, message, createChatOption);//.then((result) => {
        if (done !== undefined)
            done(null)
    //})
}

WAPP.sendLinkPreview = function(targetId, url, message, done) {

    WPP.chat.sendLinkPreview(targetId, url, message, createChatOption);//.then((result) => {
        if (done !== undefined)
            done(null)
    //})
}

WAPP.sendFile = function(targetId, file64, options, done) {

    Object.assign(options, createChatOption);

    WPP.chat.sendFileMessage(targetId, file64, options);//.then((result) => {
        if (done !== undefined)
            done(null)
    //})
}

WAPP.sendCard = function(targetId, file64, done) {

    console.log('vcard...');
    //console.log(file64);

    let base64 = file64.split(',')[1];
    let vcardS = Base64.decode(file64);
    let vcardO = vcardParse(vcardS); 
    let vname  = vcardO.fn;
    let vphone = vcardO.tel[0].value[0];

    console.log('VNAME: ' +vname);

    let vcardFields = {id: vphone + '@c.us', name: vname};

    //Object.assign(vcardFields, createChatOption);

    WPP.chat.sendVCardContactMessage(targetId, vcardFields, createChatOption);//.then((result) => {
        if (done !== undefined)
            done(null)
    //})
}




// https://github.com/wppconnect-team/wa-js
// https://github.com/wppconnect-team/wa-js/releases
// https://github.com/wppconnect-team/wa-js/releases/download/nightly/wppconnect-wa.js

function releaseChats()
{
    // let msgLength = Store.Chat._models.length;

    // if (msgLength > 1) 
    // {
    //     Store.Chat._models[0].delete();               
    // }    
}

function releaseMsgs()
{
    // let msgLength = Store.Msg._models.length;

    // if (msgLength > 25) 
    // {
    //     Store.Msg._models[msgLength-25].delete();               
    // }    

    tempFileData = [];
}



window.WAPP.sendBatch = function(send, done)
{ 
    console.log('sendBatch starting...');

    targetIdWithAt = 
        send.batch.target.address + '@c.us';

    WAPP.checkNumberStatus(targetIdWithAt, function(contact)
    { 
        send = Object.assign(
            send,
            {
                href: '/send',
                jid: contact.id,
                status: contact.status
            }
        );

        if (contact.status == 200)
        {
            //send.batch.messages.forEach((msg, msgIndex) => {

            //    console.log('delay to send: ' + (msgIndex * 1000));

                
                function sendMessage(index) {
                    if (index < send.batch.messages.length) {

                        msg = send.batch.messages[index];


                        //ENVIA
                        if ((msg.text) && (msg.files.length == 0)) {
                            console.log('TEXT...');

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
                            console.log('FILE...');
                            
                            file = msg.files[0];
                            console.log('FILE [1]');

                            //console.log('FILE,,,');
                            if (file.fileType == 'IMAGE') 
                                fileCaption = decodeURIComponent(file.caption); else
                            if (file.fileType == 'VIDEO') 
                                fileCaption = decodeURIComponent(file.caption); else
                                fileCaption = file.fileName;    
                                
                            console.log('FILE [2]');
                            fileName    = file.fileName; 
                            console.log('FILE [3]');
                            fileCaption = decodeURIComponent(file.caption);   
                            console.log('FILE [4]');
                            fileBase64  = "data:" + file.fileMime + ";base64," + tempFileData[index].data; //file.fileData;    
                            console.log('FILE [5]');

                            let fileOptions = {
                                type: 'auto-detect', 
                                isPtt: true, 
                                filename: fileName, 
                                caption: fileCaption
                            };
                            console.log('FILE [6]');

                            if (file.fileType == 'AUDIO')
                                Object.assign(fileOptions, {isPtt: true, type: 'audio'});

                            console.log('FILE [7]');

                            if (file.fileType == 'VCARD') 
                                WAPP.sendCard(send.jid, tempFileData[index].data); else
                                WAPP.sendFile(send.jid, fileBase64, fileOptions);

                            console.log('FILE [8]');

                            fileName    = '';
                            fileCaption = '';
                            fileBase64  = '';
                        }


                        //CLEAR
                        msg.text.body = 'cleaned!';
                    
                        if (msg.files.length > 0) {

                            msg.files[0].filePath = 'cleaned!';  
                            msg.files[0].fileData = 'cleaned!';  
                            msg.files[0].caption  = 'cleaned!';  
                        }

                        if (index < (send.batch.messages.length-1)) {
                            setTimeout(function () {
                                sendMessage(index + 1);
                            }, 5000);
                        } else {
                            sendMessage(index + 1)
                        }



                    } else {
                        try {

                            console.log(JSON.stringify( send ));
                            //console.log('console END!! Msg: ' + Store.Msg._models.length);
                        } catch (e) {
                            console.log('OPAAAAAAa: ' + e.message);
                        }

                        console.log('sent all');
                        releaseMsgs();
                        console.log('finished');
                    }

                }
                    
                
                sendMessage(0);


               // console.log('SENTTTTTTT: ' + (msgIndex * 1000));
            //});
        } else {

            try {
                console.log(JSON.stringify( send ));
                //console.log('console END!! Msg: ' + Store.Msg._models.length);
            } catch (e) {
                console.log('OPAAAAAAa: ' + e.message);
            }

        }



    });
}

console.log('---=> 50_batch.js');