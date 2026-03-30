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
            before: function() {

                let uid = localStorage.pr_tachyon_auth_dest_id;

                console.log(JSON.stringify(
                    {
                        href: "/profile",
                        uid: uid,
                        address: uid,
                        name: uid
                        //ip: '',
                    }
                ));
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
