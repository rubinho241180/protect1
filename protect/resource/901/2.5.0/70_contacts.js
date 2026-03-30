WAPP.getJsonMyContacts = function()
{
    
    var participants = [
    ];

    WAPI.getMyContacts().forEach(function(participant) {
        participants.push(
            {
                id: participant.id._serialized, 
                name: participant.name
            }
        );


        if (participants.length > 499)
        {
            console.log(JSON.stringify(
                {
                    href: '/contacts',
                    finished: false,
                    items: {
                        listHelper: [participants.length],
                        items: participants
                    }
                }
            ));

            participants = [];
        }
    });

    console.log(JSON.stringify(
        {
            href: '/contacts',
            finished: true,
            items: {
                listHelper: [participants.length],
                items: participants
            }
        }
    ));
}

WAPP.getJsonMyMessagesContacts = function()
{
    
    var temps = {};


    Store.Msg._models.forEach(function(participant) {

        if (
                !!participant.__x_senderObj && 
                !!participant.__x_senderObj.isWAContact && 
                 !participant.__x_sender.__x_isGroupMsg && 
                 !participant.__x_senderObj.isMyContact && 
                 !participant.__x_senderObj.isMe 
                /*&& (participant.__x_to.user == '558198636365')*/
            )
            temps[participant.__x_senderObj.__x_id._serialized] = {
                id: participant.__x_senderObj.__x_id._serialized,
                name: participant.__x_senderObj.notifyName,
                
                //pushName: participant.__x_senderObj.pushname,
                //verifiedName: participant.__x_senderObj.verifiedName,
                //type: participant.__x_type,
                //ttme: new Date(parseInt(participant.__x_t.toString() + '000')),
                //text: participant.__x_body,
                
            };
            

    });


//console.log(temps);
//console.log(Object.values(temps));
//return false;


    var participants = [

    ];

    Object.values(temps).forEach(function(participant) {

        participants.push(
            {
                id: participant.id, 
                name: participant.name || '',
               // mentionName: participant.__x_senderObj.mentionName,
                //notifyName: participant.__x_senderObj.notifyName
            }
        );


        if (participants.length > 499)
        {
            console.log(JSON.stringify(
                {
                    href: '/contacts',
                    finished: false,
                    items: {
                        listHelper: [participants.length],
                        items: participants
                    }
                }
            ));

            participants = [];
        }
    });

    console.log(JSON.stringify(
        {
            href: '/contacts',
            finished: true,
            items: {
                listHelper: [participants.length],
                items: participants
            }
        }
    ));
}
console.log('---=> 70_contacts.js');
