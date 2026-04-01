createChatOption = {createChat: true};



// WAPP.checkNumberStatus = function(targetId, done) {



//     WPP.contact.queryExists(targetId).then((result) => {

//         if (done !== undefined)

//             done({

//                 // id: !!result ? result.wid : null,
//                 id: result ? (result.lid ? result.lid : result.wid) : null,
                
//                 status: !!result ? 200 : 404

//             })

//     })

// }



// WAPP.checkNumberStatus = function(targetId, done) {

//     try {
    
//         console.log('queryExists #1');
//         WPP.contact.queryExists(targetId).then((result) => {

//             if (done !== undefined)

//                 done({

//                     // id: !!result ? result.wid : null,
//                     id: result ? (result.lid ? result.lid : result.wid) : null,
                    
//                     status: !!result ? 200 : 404

//                 })

//         })

//     } catch(error) {


//         console.log('queryExists #2');
//         setTimeout(function() {
//             console.log('queryExists.timeout #2');

//             WPP.contact.queryExists(targetId).then((result) => {

//                 if (done !== undefined)

//                     done({

//                         // id: !!result ? result.wid : null,
//                         id: result ? (result.lid ? result.lid : result.wid) : null,
                        
//                         status: !!result ? 200 : 404

//                     })

//             })

//         }, 5000);

//     }


// }

WAPP.checkNumberStatus = function (targetId, done) {

    console.log('queryExists #1');

    WPP.contact.queryExists(targetId)
        .then((result) => {

            if (done !== undefined)
                done({
                    id: result ? (result.lid ? result.lid : result.wid) : null,
                    status: result ? 200 : 404
                });

        })
        .catch((error) => {

            console.log('Erro na primeira tentativa:', error);
            console.log('queryExists #2 (retry em 5s)');

            setTimeout(() => {

                console.log('queryExists.timeout #2');

                WPP.contact.queryExists(targetId)
                    .then((result) => {

                        if (done !== undefined)
                            done({
                                id: result ? (result.lid ? result.lid : result.wid) : null,
                                status: result ? 200 : 404
                            });

                    })
                    .catch((error2) => {

                        console.log('Erro na segunda tentativa:', error2);

                        if (done !== undefined)
                            done({
                                id: null,
                                status: 500,
                                error: error2?.message || 'Erro após retry'
                            });

                    });

            }, 5000);

        });

};


// WAPP.sendText = function(targetId, message, done) {



//     WPP.chat.sendTextMessage(targetId, message, createChatOption);//.then((result) => {

//         if (done !== undefined)

//             done(null)

//     //})

// }





WAPP.sendText = function(targetId, message, done) {

    const chatOptions = {...createChatOption};

    const mentions = [];

    

    // O texto deve manter o formato @número na mensagem

    // Apenas extrair os números para o mentionedList

    const mentionRegex = /@(\d+)/g;

    let match;

    

    while ((match = mentionRegex.exec(message)) !== null) {

        const phoneNumber = match[1];

        mentions.push(phoneNumber + '@s.whatsapp.net');

    }

    

    // Adicionar menções às opções se existirem

    if (mentions.length > 0) {

        chatOptions.mentionedList = mentions;

    }

    

    // Enviar a mensagem SEM modificar o texto

    console.log('will send...');

    WPP.chat.find(targetId);

    WPP.chat.sendTextMessage(targetId, message, chatOptions);

    console.log('sent!!!');

    if (done !== undefined) {

        done(null);

    }

}





// WAPP.sendText = function(targetId, message, done) {

//     // Detectar menções na mensagem (padrão @XXXXX)

//     const mentionRegex = /@(\S+)/g;

//     const mentions = [];

//     let cleanMessage = message;

//     let match;

    

//     // Resetar o índice da regex para uma nova busca

//     mentionRegex.lastIndex = 0;

    

//     while ((match = mentionRegex.exec(message)) !== null) {

//         const mentionedUser = match[1]; // Remove o @ automaticamente

//         mentions.push(mentionedUser + '@s.whatsapp.net');

//     }

    

//     // Remover todos os @ da mensagem

//     cleanMessage = message.replace(/@/g, '');

    

//     // Criar opções do chat

//     const chatOptions = {...createChatOption};

//     if (mentions.length > 0) {

//         chatOptions.mentionedList = mentions;

//     }

    

//     WPP.chat.sendTextMessage(targetId, cleanMessage, chatOptions);

//     if (done !== undefined) {

//         done(null);

//     }

// }



WAPP.sendLinkPreview = function(targetId, url, message, done) {



    WPP.chat.sendLinkPreview(targetId, url, message, createChatOption);//.then((result) => {

        if (done !== undefined)

            done(null)

    //})

}



WAPP.sendFile = function(targetId, file64, options, done) {



    Object.assign(options, createChatOption);



    WPP.chat.find(targetId);

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



    console.log(vcardFields);



    WPP.chat.sendVCardContactMessage(targetId, vcardFields, createChatOption);//.then((result) => {

        if (done !== undefined)

            done(null)

    //})

}







WAPP.sendButtons = function(targetId, message, file64, done) {



    const transformButtons = (data) => {

      return {

        ...data,

        buttons: data.buttons.map(button => {

          const { type, text, value } = button;

          

          if (type === "URL") {

            return {

              text,

              url: value

            };

          }

          

          if (type === "PHONE") {

            return {

              text,

              phoneNumber: value

            };

          }

          

          return button;

        })

      };

    };



    //console.log('buttons...');



    let base64 = file64.split(',')[1];

    let btnStr = Base64.decode(file64);

    let btnObj = transformButtons(JSON.parse(btnStr));







    //console.log('btnStr: ' +JSON.stringify(btnObj));



    //Object.assign(vcardFields, createChatOption);





    let sendObject = Object.assign({}, btnObj, {useInteractiveMessage: true});



    //console.log('END: '+ targetId + ': ' + JSON.stringify(sendObject));



    WPP.chat.sendTextMessage(targetId, message, sendObject);    



    if (done !== undefined)

        done(null)

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



    isGroup = send.batch.target.address.includes('@g.us');



    if (isGroup) {

        targetCheckId = WPP.conn.getMyUserId()._serialized;

        targetIdWithAt = send.batch.target.address;

    } else {

        targetIdWithAt = send.batch.target.address + '@c.us';

        targetCheckId = targetIdWithAt;

    }





    // if (!send.batch.target.address.includes('@')) {

    //     targetIdWithAt = targetIdWithAt + '@c.us';

    // }





    WAPP.checkNumberStatus(targetCheckId, function(contact)

    { 

        console.log('checked1', contact);

        send = Object.assign(

            send,

            {

                href: '/send',

                jid: isGroup ? targetIdWithAt : contact.id,

                status: contact.status

            }

        );



        console.log('checked2', send);

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

                            if (file.fileType == 'BUTTO') 

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

                                caption: fileCaption,

                                createChat: true

                            };

                            console.log('FILE [6]');



                            if (file.fileType == 'AUDIO')

                                Object.assign(fileOptions, {isPtt: true, type: 'audio'});



                            console.log('FILE [7]');



                            if (file.fileType == 'VCARD') 

                                WAPP.sendCard(send.jid, tempFileData[index].data); else



                            if (file.fileType == 'BUTTO') 

                                WAPP.sendButtons(send.jid, fileCaption, tempFileData[index].data); else

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