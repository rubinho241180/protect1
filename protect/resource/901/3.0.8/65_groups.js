WAPP.getMyGroups = async function()
{
    let result = await WPP.group.getAllGroups();
    let output = [];

    result.forEach(g => {
        if (!!g && !g.__x_isDeprecated)
            output.push({
            uid: g.__x_id, 
            name: g.__x_formattedTitle
        })
    })

    console.log(JSON.stringify(
        {
            href: '/groups',
            items: output
        }
    ));
}

WAPP.getMyGroupParticipants = async function(id) {
    console.log(id);

    let result = await WPP.group.getParticipants(id);
    let output = [];

    result.forEach(p => {
        if (!!p)
            output.push({
            address: p.__x_id.user,
            name: p.__x_contact.__x_name || p.__x_contact.__x_verifiedName || p.__x_contact.__x_notifyName || '' 
        })
    })
    
    // const output = ().map((p) => 
    //     ({
    //         address: p.__x_id.user, 
    //         name: p.__x_contact.__x_verifiedName || p.__x_contact.__x_notifyName || ''
    //     })
    // )

    console.log(JSON.stringify(
        {
            href: '/participants',
            items: output
        }
    ));
}

console.log('---=> 65_groups.js');