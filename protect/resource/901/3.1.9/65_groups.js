WAPP.getMyGroups = async function()
{
    let result = await WPP.group.getAllGroups();
    let output = [];

    result.forEach(g => {
        //if (!!g && !g.__x_isDeprecated)
        if (!!g)
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

// WAPP.getMyGroupParticipants = async function(id) {
//     console.log(id);

//     let result = await WPP.group.getParticipants(id);
//     let output = [];

//     result.forEach(p => {
//         if (!!p)
//             output.push({
//             address: p.__x_id.user,
//             name: p.__x_contact.__x_name || p.__x_contact.__x_verifiedName || p.__x_contact.__x_notifyName || p.__x_contact.__x_pushname || '' 
//         })
//     })

//     console.log(JSON.stringify(
//         {
//             href: '/participants',
//             items: output
//         }
//     ));
// }


WAPP.getMyGroupParticipants = async function(id) {
    console.log(id);

    // Converte string em array, se necessário
    const ids = Array.isArray(id) ? id : [id];
    
    let allOutput = [];

    // Itera sobre cada ID
    for (const groupId of ids) {
        try {
            let result = await WPP.group.getParticipants(groupId);
            let output = [];

            result.forEach(p => {
                if (!!p) {
                    output.push({
                        address: p.contact.__x_phoneNumber.user,
                        name: p.__x_contact.__x_name || 
                              p.__x_contact.__x_verifiedName || 
                              p.__x_contact.__x_notifyName || 
                              p.__x_contact.__x_pushname || '' 
                    });
                }
            });

            // Adiciona informação do grupo aos participantes
            // output.forEach(participant => {
            //     participant.groupId = groupId;
            // });

            allOutput = allOutput.concat(output);
            
        } catch (error) {
            console.error(`Erro ao obter participantes do grupo ${groupId}:`, error);
        }
    }

    console.log(JSON.stringify({
        href: '/participants',
        items: allOutput,
        totalGroups: ids.length,
        totalParticipants: allOutput.length
    }));
}