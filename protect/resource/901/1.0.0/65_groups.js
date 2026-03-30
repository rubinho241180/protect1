WAPP.getJsonGroups = function()
{
    var groups = [];

    WAPI.getAllGroups().forEach(function(group)
        {
            groups.push(
                {
                    id: group.__x_id._serialized, 
                    name: group.__x_name || ''
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
    var id = new window.Store.UserConstructor(gid, {intentionallyUsePrivateConstructor: true});
    
    WAPI._getGroupParticipants(id).then(
        function(result)
        {
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
