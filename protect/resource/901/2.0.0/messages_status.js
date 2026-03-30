SEL_CONNECTED    = '.main-nav-menu:first';
SEL_SYNCING      = '.centered-spinner:not(.hide):first';
SEL_SYNCING2     = '#loader';
SEL_DISCONNECTED = '.qr-code:first';
SEL_CONFLICT     = 'mat-dialog-container:first';

SEL_NO_INTERNET1 = '.splash';
SEL_NO_INTERNET2 = '.title';
SEL_NO_INTERNET3 = '.subtitle';



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
        },
        internet: {
            has: [
                SEL_NO_INTERNET1,
                SEL_NO_INTERNET2,
                SEL_NO_INTERNET3
            ]
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

console.log('messages_status.js INJECTED!');
