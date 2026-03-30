SEL_DISCONNECTED = '.qr-code-wrapper';
SEL_CONNECTED    = '.main-nav-menu:first-child';

//device
console.log(JSON.stringify(
    {
        href: '/device',
        manufacturer: '@manufacturer',
        model: 'UNK',
    }
));


STATUS.start(
    {
        connected: {
            has: [
                SEL_CONNECTED        
            ],
            not: [
                '.bubble'
            ],
            //inject: true,
            override: function() {

                let uid = localStorage.pr_tachyon_auth_dest_id;

                console.log(JSON.stringify(
                    {
                        href: "/profile",
                        uid: threadObj.channelUId,
                        address: threadObj.channelAddress,
                        name: threadObj.channelName
                        //ip: '',
                    }
                ));

                setTimeout(() => {
                    console.log(JSON.stringify({href: "/status", status: "stLogged"}));
                }, 500);                
            }
        },
        disconnected: {
            has: [
                SEL_DISCONNECTED,
            ],
            before: function() {
                let btn = mySelectorAll('.mdc-switch--unselected');
                //btn.setAttribute('disabled', '1');
                
                if (btn.length == 1) {
                    simulateClick('.mdc-switch--unselected');
                    console.log('do checked'); 
                } else 
                    console.log('has checked');
            }
        }
    }
)


//port
//console.log(JSON.stringify(
//    {
//        href: '/port',
//        status: 200,
//        description: 'OK',
//    }
//));




console.log('jf_status_messages.js');
