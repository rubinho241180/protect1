//SEL_CONNECTED    = '[data-icon=menu]'; //'[data-icon=status-v3]';
SEL_CONNECTED    = '[data-icon=new-chat-outline]'; //'[data-icon=status-v3]';

SEL_DISCONNECTED = 'canvas[width="264"]';
SEL_SYNCING      = 'progress';
//SEL_BLOCKED      = '[data-testid=logout-reason-warning-icon]';

myExternalIP = 'null';

let astatus =
    {
        connected: {
            has: [
                SEL_CONNECTED        
            ],
            override: function() {
                
                console.log(JSON.stringify({href: "/inject"}));

                tempo = setInterval(async () => {
                    console.log("setInterval WPP...");


                    if (!!window.WPP)
                    {
                        //try {


                            //WPP.conn.getMyUserId()
                            let userObj = WPP.whatsapp.ContactStore.get(WPP.whatsapp.UserPrefs.getMaybeMeUser());

                            if (!!userObj) {

                                clearInterval(tempo);

                                //console.log(JSON.stringify(userObj));
                                let profPicUrl = await getProfilePictureUrl(userObj.id);

                                console.log(JSON.stringify(
                                    {
                                        href:"/profile",
                                        uid: userObj.id,
                                        address: userObj.id.user,
                                        name: userObj.notifyName || userObj.displayName || '',
                                        ip: myExternalIP,
                                        picUrl: profPicUrl, 
                                    }
                                ));

                                (function () {
                                  'use strict';

                                  WPP.webpack.onReady(function () {
                                    
                                    console.log(JSON.stringify({href: "/status", status: "stLogged"}));

                                    WAPP.getMyLabels();
                                    WAPP.monitoringLabels();
                                    WAPP.monitoringMessages();


                                  });

                                  // Your code here...
                                })();

                                // setTimeout(async () => {
                                //     console.log(JSON.stringify({href: "/status", status: "stLogged"}));

                                //     WAPP.getMyLabels();
                                //     let xxx = await WAPP.getMyOfflineMessages();
                                //     WAPP.monitoringLabels();
                                //     WAPP.monitoringMessages();
                                // }, 1000);

                            }
                        
                        //} catch(err) {
                        //    console.log("error waiting WPP: " + err.message);
                        //}

                    } else {
                        console.log("Waiting window.WPP...");
                    }

                }, 500);
            },

            after: function() {
                console.log('after!');
            }
        },
        disconnected: {
            has: [
                SEL_DISCONNECTED
            ],
            // not: [
                
            // ]
        },
        // blocked: {
        //     has: [
        //         SEL_DISCONNECTED
        //     ]
        // }
    };



//device (BEFORE PROXY)
// console.log(JSON.stringify(
//     {
//         href: '/device',
//         manufacturer: '@manufacturer',
//         model: 'UNK',
//     }
// ));

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




console.log('jf_status_whatsapp.js [0310231250]');
