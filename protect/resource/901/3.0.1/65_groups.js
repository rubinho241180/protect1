WAPP.getMyGroups = async function()
{
    const output = (await WPP.group.getAllGroups()).map((g) => 
        ({
            uid: g.__x_id, 
            name: g.__x_formattedTitle
        })
    )

    console.log(JSON.stringify(
        {
            href: '/groups',
            items: output
        }
    ));
}

WAPP.getMyGroupParticipants = async function(id) {
    
    const output = (await WPP.group.getParticipants(id)).map((p) => 
        ({
            address: p.__x_id.user, 
            name: p.__x_contact.__x_verifiedName || p.__x_contact.__x_notifyName || p.__x_contact.__x_pushname || ''
        })
    )

    console.log(JSON.stringify(
        {
            href: '/participants',
            items: output
        }
    ));
}

console.log('---=> 65_groups.js');