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