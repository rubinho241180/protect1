async function getProfilePictureUrl(senderId) {
  try {
    const profilePicUrl = await WPP.contact.getProfilePictureUrl(senderId);
    if (!profilePicUrl) return null;
    
    return profilePicUrl;
  } catch (error) {
    console.error('Erro ao obter foto de perfil:', error);
    return null;
  }
}


WAPP.serializeMsg = async function(msg)
{
    var obj = WAPI._serializeMessageObj(msg);

    let profPicUrl = await getProfilePictureUrl(obj.sender.id);



    var cmdMsg =
    {
        href        : '/message',
        id          : msg.__x_id.id, 

        target      : {
            address :  msg.__x_to.user,
            name    : (msg.__x_notifyName || msg.__x_to.user),
            uid     :  msg.__x_to._serialized,
        },

        sender      : {
            address : msg.senderObj.__x_id.user,
            name    : (msg.senderObj.__x_pushname || msg.senderObj.__x_name || msg.senderObj.__x_formattedName),
            uid     :  msg.senderObj.__x_id._serialized,
        },
        isSentByMe  : msg.__x_id.fromMe,
        isSentByWApp: false,  

        isDirectMsg : !msg.isGroupMsg, 

        body        : (msg.__x_type == 'chat') ? /*Base64.encode(*/msg.__x_body/*)*/ : ('msgObj.content || null'),          

        profilePicUrl: profPicUrl,
        loggedAt: WAPP.loggedAt,
        t: msg.__x_t,
        timestamp: msg.__x_t
    };

    if (msg.__x_type == "location") {
        cmdMsg.location = {
            lat: msg.__x_lat,
            lon: msg.__x_lng
        };
    }

    return cmdMsg;
}


WAPP.getChatsWithLastReceivedMessages = async function (timestamp) {
  try {
    // Lista todos os chats
    const allChats = await WPP.chat.list();
    console.log(`Encontrados ${allChats.length} chats no total`);
    
    // Filtra os chats com __x_t maior que o timestamp informado
    const relevantChats = allChats.filter(chat => chat.__x_id.server === 'c.us' && chat.__x_t > timestamp);
    console.log(`Encontrados ${relevantChats.length} chats relevantes`);
    
    // Ordena os chats por __x_t em ordem crescente
    relevantChats.sort((a, b) => a.__x_t - b.__x_t);
    
    // Para cada chat filtrado, busca a última mensagem recebida
    const chatsWithLastReceivedMessages = await Promise.all(
      relevantChats.map(async (chat) => {
        const messages = await WPP.chat.getMessages(chat.id);
        // Filtra apenas as mensagens recebidas (fromMe === false)
        const receivedMessages = messages.filter(msg => /*['chat', 'callLog'].includes(msg.kind) &&*/ !msg.__x_id.fromMe);
        // Pega a última mensagem recebida
        const lastReceivedMessage = receivedMessages.length ? receivedMessages[receivedMessages.length - 1] : null;
        return { chat, lastReceivedMessage };
      })
    );
    
    // Remove chats que não têm mensagens recebidas
    return chatsWithLastReceivedMessages.filter(item => item.lastReceivedMessage);
  } catch (error) {
    console.error('Erro ao obter chats e mensagens:', error);
    return [];
  }
}


WAPP.getMyOfflineMessages = async function(timestamp) {

    const lastChat = await WAPP.getChatsWithLastReceivedMessages(timestamp);

    lastChat.forEach(async function(chat) {
        console.log('getMyOfflineMessages', chat.lastReceivedMessage);
        var cmdMsg = await WAPP.serializeMsg(chat.lastReceivedMessage);
        console.log(JSON.stringify(cmdMsg));
    });
}


WAPP.triggerMessage = async function(msg)
{
    try
    {

       // if (
       //     (msg.__x_t > WAPP.loggedAt) && (['chat', 'callLog'].includes(msg.kind)) && (!!msg.__x_isNewMsg) && (!msg.timestamp) && (!msg.__x_subtype))
        //{
            //console.log('triggerMessage', msg);
            

            var cmdMsg = await WAPP.serializeMsg(msg)
            //msg.timestamp = 1;

            if (cmdMsg.isDirectMsg && !cmdMsg.isSentByMe) {
                
                console.log(JSON.stringify(cmdMsg));
            }

        //} else {
           // console.log('else');
        //}
    }
    catch(err)
    {
       console.log('ERROR: '+err.message);
    }
}


WAPP.monitoringMessages = function()
{
    WPP.whatsapp.MsgStore.off('add').on('add', (msg) => {

        if (msg.__x_t > WAPP.loggedAt) 
        {
            console.log('OnMsg', msg);
            WAPP.triggerMessage(msg);
        }
    });
    console.log('monitoringMessages: ON;');
}


window.WAPP.loggedAt = parseInt(Date.now().toString().substring(0, 10));

// setTimeout(() => {
//     WAPP.monitoringMessages();
// }, 2500);


console.log('---=> 60_messages.js');
