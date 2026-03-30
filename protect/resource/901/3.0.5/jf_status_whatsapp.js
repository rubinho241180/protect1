SEL_CONNECTED    = '[data-icon=menu]'; //'[data-icon=status-v3]';
SEL_DISCONNECTED = 'canvas[width="264"]';
SEL_SYNCING      = 'progress';
SEL_BLOCKED      = '[data-testid=logout-reason-warning-icon]';

myExternalIP = 'null';

let astatus =
    {
        connected: {
            has: [
                SEL_CONNECTED        
            ],
            override: function() {
                
                console.log(JSON.stringify({href: "/inject"}));

                tempo = setInterval(() => {


                    if (typeof Store != "undefined")
                    {

                        clearInterval(tempo);

                        //WPP.conn.getMyUserId()
                        //let userObj = WPP.whatsapp.ContactStore.get(WPP.whatsapp.UserPrefs.getMaybeMeUser());

                        userObjId = localStorage.getItem('last-wid-md').replace('"', '').split(':')[0];

                        console.log(JSON.stringify(
                            {
                                href:"/profile",
                                uid: userObjId + '@c.us',
                                address: userObjId,
                                name: userObjId, //userObj.notifyName || userObj.displayName || '',
                                ip: myExternalIP,
                            }
                        ));

                        console.log(JSON.stringify({href: "/status", status: "stLogged"}));
                    }


                }, 500);
            },

            after: function() {
                console.log('after!');
            }
        },
        disconnected: {
            has: [
                SEL_DISCONNECTED,
            ],
            not: [
                SEL_BLOCKED
            ]
        },
        blocked: {
            has: [
                SEL_BLOCKED,
                SEL_DISCONNECTED
            ]
        }
    };



//device
console.log(JSON.stringify(
    {
        href: '/device',
        manufacturer: '@manufacturer',
        model: 'UNK',
    }
));

// fetch("https://api.ipify.org")
//   .then(res => res.text())
//   .then(data => {
//     myIP = data;
    STATUS.start(astatus);
// });

//port
//console.log(JSON.stringify(
//    {
//        href: '/port',
//        status: 200,
//        description: 'OK',
//    }
//));




console.log('jf_status_whatsapp.js [INJECTED]');
