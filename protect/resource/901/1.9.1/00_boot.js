function isLoggedIn (done) {
    return !!document.querySelector("[data-asset-intro-image-light]");
};


window.myint2 = setInterval(function(){

    if (!!isLoggedIn())
    {
            //console.log('Status FOUND!');
        clearInterval(window.myint2);
    
        console.log(JSON.stringify(
            {
                href  : '/injectable',
                time  : Date.now(),
            }
        ));
    } //else {console.log('not still')};

}, 500);


//port
console.log(JSON.stringify(
    {
        href: '/port',
        status: 200,
        description: 'OK',
    }
));


console.log('---=> 00_boot.js');