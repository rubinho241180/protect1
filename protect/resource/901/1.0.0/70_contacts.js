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
    });

    console.log(JSON.stringify(
        {
            href: '/contacts',
            items: {
                listHelper: [participants.length],
                items: participants
            }
        }
    ));
}