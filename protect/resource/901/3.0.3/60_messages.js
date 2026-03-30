WAPP.triggerMessage2 = function(msg)
{
    try
    {

        if ((msg.__x_t > WAPP.loggedAt) && (!!msg.__x_isNewMsg) && (!msg.timestamp) && (!msg.__x_subtype))
        {
            var obj = 
            WAPI._serializeMessageObj(msg);

            //console.log('here: ' + JSON.stringify(msgObj));

            var cmdMsg =
            {
                href        : '/message',
                id          : obj.id, 

                target      : {
                    id      : obj.chat.contact.id.toString().split('@')[0],
                    name    : obj.chat.contact.pushname || obj.chat.contact.name || obj.chat.contact.formattedName,
                },

                sender      : {
                    id      : obj.sender.id.toString().split('@')[0],
                    name    : obj.sender.pushname || obj.sender.name || obj.sender.formattedName,
                },
                /*
                msgType     : WAPP.msgTypeToMyMsgType[msgObj.type],                 
                */
                isSentByMe  : obj.fromMe,
                isSentByWApp: msg.__x_local ? msg.__x_local : false,  

                isDirectMsg : !obj.isGroupMsg, 

                body        : (obj.type == 'chat') ? /*Base64.encode(*/obj.content/*)*/ : ('msgObj.content || null'),          

                /*
                file        : msgObj.mimetype ? 
                                                {
                                                    name: (msgObj.filename || null), 
                                                    mimeType: msgObj.mimetype, 
                                                    type: msgObj.type,
                                                } : null,
                caption     : msgObj.caption ? Base64.encode(msgObj.caption) : null, 
                */
            };

            if (msg.__x_type == "location") {
                cmdMsg.location = {
                    lat: msg.__x_lat,
                    lon: msg.__x_lng
                };
            }

            msg.timestamp = 1;
            console.log(JSON.stringify(cmdMsg));

        }
    }
    catch(err)
    {
        console.log('ERROR: '+err.message);
    }
}

WAPP.triggerMessage = function(msg)
{
    try
    {

        if ((msg.__x_t > WAPP.loggedAt) && (!!msg.__x_isNewMsg) && (!msg.timestamp) && (!msg.__x_subtype))
        {
//onsole.log(msg);
            var obj = 
            WAPI._serializeMessageObj(msg);
//console.log(msgObj);
            //console.log('here: ' + JSON.stringify(msgObj));

            var cmdMsg =
            {
                href        : '/message',
                id          : msg.__x_id.id, 

                target      : {
                    address : msg.__x_to.user,
                    name    : (msg.__x_notifyName || msg.__x_to.user),
                },

                sender      : {
                    address : msg.senderObj.__x_id.user,
                    name    : (msg.senderObj.__x_pushname || msg.senderObj.__x_name || msg.senderObj.__x_formattedName),
                },
                /*
                msgType     : WAPP.msgTypeToMyMsgType[msgObj.type],                 
                */
                isSentByMe  : msg.__x_id.fromMe,
                isSentByWApp: false, //msg.__x_local ? msg.__x_local : false,  

                isDirectMsg : !msg.isGroupMsg, 

                body        : (msg.__x_type == 'chat') ? /*Base64.encode(*/msg.__x_body/*)*/ : ('msgObj.content || null'),          

                /*
                file        : msgObj.mimetype ? 
                                                {
                                                    name: (msgObj.filename || null), 
                                                    mimeType: msgObj.mimetype, 
                                                    type: msgObj.type,
                                                } : null,
                caption     : msgObj.caption ? Base64.encode(msgObj.caption) : null, 
                */
                loggedAt: WAPP.loggedAt,
                t: msg.__x_t
            };

            if (msg.__x_type == "location") {
                cmdMsg.location = {
                    lat: msg.__x_lat,
                    lon: msg.__x_lng
                };
            }

            msg.timestamp = 1;

            if (cmdMsg.isDirectMsg && !cmdMsg.isSentByMe)
                console.log(JSON.stringify(cmdMsg));

        }
    }
    catch(err)
    {
        console.log('ERROR: '+err.message);
    }
}


WAPP.monitoringMessages = function()
{
    window.Store.Msg.off('add').on('add', (msg) => WAPP.triggerMessage(msg));
    console.log('monitoringMessages: ON;');
}


window.WAPP.loggedAt = parseInt(Date.now().toString().substring(0, 10));

//setTimeout(() => {
    WAPP.monitoringMessages();
//}, 2500);

console.log('---=> 60_messages.js');
