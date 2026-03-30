SEL_CONNECTED    = '#LeftColumn';
//SEL_CONNECTED    = '#page-chats';
SEL_DISCONNECTED = '#auth-qr-form';


//device
console.log(JSON.stringify(
    {
        href: '/device',
        manufacturer: '@manufacturer',
        model: 'UNK',
    }
));


let astatus =
    {
        connected: {
            has: [
                SEL_CONNECTED        
            ],
            not: [
                '.Loading.interactive',
                '.chat-list .Loading'
            ],
            //inject: true,
            before: function() {

                //let userId  = JSON.parse(localStorage.user_auth).id;
                let dataObj = JSON.parse(localStorage.getItem('tt-global-state'));
                let userObj = dataObj.users.byId[dataObj.currentUserId];

                console.log(JSON.stringify(
                    {
                        href:"/profile",
                        uid: dataObj.currentUserId,
                        address: userObj.phoneNumber,
                        name: userObj.firstName,
                        ip: myIP
                    }
                ));
            },

            after: function() {
                console.log('after!');
            }
        },
        disconnected: {
            has: [
                SEL_DISCONNECTED,
            ]
        }
    }


fetch("https://api.ipify.org")
  .then(res => res.text())
  .then(data => {
    myIP = data;
    STATUS.start(astatus);
});




//port
//console.log(JSON.stringify(
//    {
//        href: '/port',
//        status: 200,
//        description: 'OK',
//    }
//));




console.log('jf_status_telegram.js');
