WAPP.getJsonGroups = function()
{
    var groups = [];

    WAPI.getAllGroups().forEach(function(group)
        {
            groups.push(
                {
                    id: group.__x_id._serialized, 
                    name: group.__x_formattedTitle
 || ''
                }
            );
        }
    );

    console.log(JSON.stringify(
        {
            href: '/groups',
            items: {
                listHelper: [groups.length],
                items: groups
            }
        }
    ));
}

WAPP.getJsonGroupParticipants = function(gid)
{
    console.log('getJsonGroupParticipants: 1');
    console.log('gid: '+gid);
    var id = new window.Store.UserConstructor(gid, {intentionallyUsePrivateConstructor: true});
    console.log('getJsonGroupParticipants: 2');
    
    WAPI._getGroupParticipants(id).then(
        function(result)
        {
            console.log('getJsonGroupParticipants: 3');
            var participants = [
            ];

            result._models.forEach(function(participant) {
                if (!participant.__x_contact.__x_isMe)
                {
                    participants.push(
                        {
                            id: participant.__x_contact.__x_id._serialized, 
                            name: participant.__x_contact.__x_name || participant.__x_contact.__x_notifyName || ''
                        }
                    );
                }
            });

            console.log(JSON.stringify(
                {
                    href: '/participants',
                    items: {
                        listHelper: [participants.length],
                        items: participants
                    }
                }
            ));

        }
    );
}

WAPP.getJsonGroupParticipants4 = async function(id) {
    console.log(id);
    const output = (await WAPI._getGroupParticipants(id))
        .map((participant) => participant.id);

    //if (done !== undefined) done(output);
    console.log(JSON.stringify(output));
           
    return output;
};