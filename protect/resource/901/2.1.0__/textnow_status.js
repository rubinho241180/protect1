SEL_CONNECTED    = '#newText:first';
SEL_SYNCING      = '.centered-spinner:not(.hide):first';
SEL_SYNCING2     = '#loader';
SEL_DISCONNECTED = '#txt-username:first';
SEL_CONFLICT     = 'mat-dialog-container:first';



STATUS.start(
    {
        syncing: {
            has: [
                SEL_SYNCING,
            ]
        },
        conflict: {
            has: [
                SEL_CONFLICT,
            ]
        },
        connected: {
            has: [
                SEL_CONNECTED
            ],
            not: [
                SEL_SYNCING,
                SEL_SYNCING2,
                SEL_CONFLICT,
            ]
        },
        disconnected: {
            has: [
                SEL_DISCONNECTED,
            ]
        }
    },
    
    {
        connected: {
            override: function()
            {
                WAPP.profile.id = $('.phoneNumber ').text().trim();

                //profile
                console.log(JSON.stringify(
                    {
                        href: '/profile',
                        id: WAPP.profile.id.replace(/[^0-9]/g, ''),
                        name: WAPP.profile.id,
                        formattedId: WAPP.profile.id,
                    }
                ));

                 //status
                STATUS.trigger(
                    'stLogged'
                );
           }
        }
    }
    
);


//port
console.log(JSON.stringify(
    {
        href: '/port',
        status: 200,
        description: 'OK',
    }
));

//device
console.log(JSON.stringify(
    {
        href: '/device',
        manufacturer: 'man',
        model: 'UNK',
    }
));

console.log('textnow_status.js INJECTED!');
