function outputContacts(items, finished) {
    
    console.log(JSON.stringify(
        {
            href: '/contacts',
            finished: finished,
            items: items
        }
    ));      
}

WAPP.getMyContacts = async function() {
    
    const contacts = (await WPP.contact.list())
        
        .filter(c => (
            !!c.isMyContact && 
            !c.isGroup && 
            (c.__x_id.server === 'c.us')
        )) 
        .map((c) => 
        ({
            address: c.__x_id.user, 
            name: c.__x_name || c.__x_verifiedName || c.__x_notifyName || ''
        })
    )

    output = [];

    contacts.forEach((contact) => {

        output.push( contact );

        if (output.length > 499) {

            outputContacts(output, false);
            output = [];
        }
    })    

    outputContacts(output, true);
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
    
    const contacts = (await WPP.contact.list())
        
        .filter(c => (
            !c.isMyContact && 
            !c.isGroup && 
            (c.__x_id.server === 'c.us')
        )) 
        .map((c) => 
        ({
            address: c.__x_id.user, 
            name: c.__x_name || c.__x_verifiedName || c.__x_notifyName || ''
        })
    )

    // const contacts = (await WPP.chat.list())

    //     .filter(c => (
    //         !c.contact.isMyContact && 
    //         !c.contact.isGroup &&
    //         (c.contact.id.server === 'c.us')
    //     )) 
    //     .map((c) => 
    //         ({
    //             address: c.contact.id.user, 
    //             name: c.contact.__x_name || c.contact.__x_verifiedName || c.contact.__x_notifyName || c.contact.__x_pushname || c.contact.__x_premiumMessageName || ''
    //         })
    //     )

    output = [];

    contacts.forEach((contact) => {

        output.push( contact );

        if (output.length > 499) {

            outputContacts(output, false);
            output = [];
        }
    })    

    outputContacts(output, true);

}

console.log('---=> 70_contacts.js');
