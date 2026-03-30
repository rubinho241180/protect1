WAPP.triggerMessage = function(msg)
{
    try
    {

        if (/*(WAPP.supportedMsgTypes.indexOf(msg.__x_type) > -1) &&*/ (!!msg.__x_isNewMsg) && (!msg.timestamp) && (!msg.__x_subtype))
        {
            var msgObj = 
            WAPI._serializeMessageObj(msg);

            var cmdMsg =
            {
                href        : '/message',
                id          : msgObj.id, 

                target      : {
                    id      : msgObj.chat.contact.id.toString().split('@')[0],
                    name    : (msgObj.chat.contact.pushname || msgObj.chat.contact.name || msgObj.chat.contact.formattedName),
                },

                sender      : {
                    id      : msgObj.sender.id.toString().split('@')[0],
                    name    : (msgObj.sender.pushname || msgObj.sender.name || msgObj.sender.formattedName),
                },
                /*
                msgType     : WAPP.msgTypeToMyMsgType[msgObj.type],                 
                */
                isSentByMe  : msg.__x_id.fromMe,
                isSentByWApp: msg.__x_local ? msg.__x_local : false,  

                isDirectMsg : !msgObj.isGroupMsg, 

                body        : (msgObj.type == 'chat') ? /*Base64.encode(*/msgObj.content/*)*/ : (msgObj.content || 'null'),          

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

WAPP.monitoringMessages = function()
{
    window.Store.Msg.off('add').on('add', (msg) => WAPP.triggerMessage(msg));
    console.log('monitoringMessages: ON;');
}

console.log('---=> 60_messages.js');
