WAPP.getMyContacts = async function() {
    
    const output = (await WPP.contact.list())
        
        .filter(c => (
            !!c.isMyContact && 
            !c.isGroup
        )) 
        .map((c) => 
        ({
            address: c.__x_id.user, 
            name: c.__x_name || c.__x_verifiedName || c.__x_notifyName || ''
        })
    )

    console.log(JSON.stringify(
        {
            href: '/contacts',
            finished: true,
            items: output
        }
    ));    
}


// WAPP.getMyMessagesContacts = async function() {
    
//     const output = (await WPP.contact.list())
        
//         .filter(c => (
//             !c.isMyContact && 
//             !c.isGroup
//         )) 
//         .map((c) => 
//         ({
//             address: c.__x_id.user, 
//             name: c.__x_name || c.__x_verifiedName || c.__x_notifyName || ''
//         })
//     )

//     console.log(JSON.stringify(
//         {
//             href: '/contacts',
//             finished: true,
//             items: output
//         }
//     ));    
// }


WAPP.getMyMessagesContacts = async function() {
    
    const output = (await WPP.chat.list())

        .filter(c => (
            !c.contact.isMyContact && 
            !c.contact.isGroup
        )) 
        .map((c) => 
            ({
                address: c.contact.id.user, 
                name: c.contact.__x_name || c.contact.__x_verifiedName || c.contact.__x_notifyName || c.contact.__x_pushname || c.contact.__x_premiumMessageName || ''
            })
        )

    console.log(JSON.stringify(
        {
            href: '/contacts',
            finished: true,
            items: output
        }
    ));    
}

console.log('---=> 70_contacts.js');
