function isLoggedIn (done) {
    return !!document.querySelector("[data-icon=menu]");
};

function isLoggedOut() {
    return !!document.querySelector("[data-testid=qrcode]");
}

// function myStatus() {
//     return isLoggedOut() ? false : isLoggedIn();
// }
function myStatus() {
    return isLoggedOut() ? false : (isLoggedIn() ? true : undefined);
}

window.InjectScript = {
    status: undefined,
};

window.myint2 = setInterval(function(){

    if (myStatus() != window.InjectScript.status) 
    {

        window.InjectScript.status = myStatus();

        if (window.InjectScript.status == false) 
        {
            console.log(JSON.stringify(
                {
                    href  : '/status',
                    status: 'stUnLogged',
                    time  : Date.now(),
                }
            ))

        } else

        if ((window.InjectScript.status == true) && (!window.Store))
        {
            console.log(JSON.stringify(
                {
                    href  : '/inject',
                    time  : Date.now(),
                }
            ));
        } else

        console.log('undefined...');
    }

}, 500);


//port
// console.log(JSON.stringify(
//     {
//         href: '/port',
//         status: 200,
//         statusText: 'OK',
//         description: 'OK',
//     }
// ));


console.log('---=> 00_boot.js');