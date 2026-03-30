/*
function isLoggedIn (done) {
    return !!document.querySelector("[data-testid=chat]");
};

function isLoggedOut (done) {
    return !!document.querySelector(".landing-header");
};
*/


//port
console.log(JSON.stringify(
    {
        href: '/port',
        status: 200,
        description: 'OK',
    }
));

/*
window.myStatus = 'stUnknown';

window.myint2 = setInterval(function(){


    if (isLoggedIn())
    {
            
        clearInterval(window.myint2);
    
        console.log(JSON.stringify(
            {
                href  : '/injectable',
                time  : Date.now(),
            }
        ));

    } else 

    if (isLoggedOut()) {


        if (window.myStatus != 'stUnLogged')
        {
            window.myStatus = 'stUnLogged';   
    
            //status
            console.log(JSON.stringify(
                {
                    href: '/status',
                    status: window.myStatus,
                    mode: {},
                }
            ));
        }

        console.log('not still')

    };

}, 500);
*/

SEL_CONNECTED    = '[data-testid=chat]';
SEL_DISCONNECTED = '.landing-header';


STATUS.start(
    {
        connected: {
            has: [
                SEL_CONNECTED
            ]
        },
        disconnected: {
            has: [
                SEL_DISCONNECTED,
            ]
        }
    },

    //override
    {
        connected: {
            override: function()
            {
                console.log(JSON.stringify(
                    {
                        href  : '/injectable',
                        time  : Date.now(),
                    }
                ));

                //status
                //STATUS.trigger(
                //    'stLogged'
                //);
            }
        }
    }
);




console.log('---=> 00_boot.js 2.0.0');