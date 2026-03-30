window.Base64 = {
    encode: function(str)
    {
        //return window.btoa(encodeURIComponent(str));
        return window.btoa(unescape(encodeURIComponent( str )));
    },
    decode: function(str)
    {
        //return decodeURIComponent(window.atob(str));
        return decodeURIComponent(escape(window.atob( str )));
    }
}

var iniToObj = function (url) {
    var params = {};
    var parser = document.createElement('a');
    url = url.replace(/(\n)/g, "&");
    parser.href = '?'+url;
    var query = parser.search.substring(1);
    var vars = query.split('&');
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
        params[pair[0]] = decodeURIComponent(pair[1]);
    }
    return params;
};



function mySelectorAll(el) {

    var result = [];

    if (!!el.trim() == '')
        return result;

    //return document.querySelectorAll(el);



    el.split(',').forEach((each) => {
        //console.log(each);

        if (each.includes(':')) {

            let ael = document.querySelector(each);

            if (!!ael)
                result.push( ael );
        } else {
            document.querySelectorAll(each).forEach((e) => {
                result.push(e)
            });        
        }
    });

    return result;  


/*


    el.split(',').forEach((each) => {
        
        //console.log('each', each);

        //try {
    
            var each_parts = each.split(':');
            var each_selec = '';
            //var each_sufix = each_parts[each_parts.length-1];
            var each_items = [];

            each_parts.forEach((x) => {
                //console.log('parts', x);
            }) ;

            if (each.endsWith(':first')) {
                each_parts.pop();
                each_selec = each_parts.join(':');
                each_items = document.querySelectorAll(each_selec);
                
                if (each_items.length > 0)
                    result.push(each_items[0]);
            } else

            if (each.endsWith(':last')) {
                each_parts.pop();
                each_selec = each_parts.join(':');
                each_items = document.querySelectorAll(each_selec);

                if (each_items.length > 0)
                    result.push(each_items[each_items.length-1]);
            } else

            document.querySelectorAll(each).forEach((e) => {
                result.push(e);
            });

        //}
        //catch(err) {
            //
        //    console.log('ops: "' + el + '" ['+ each +'] = ', err.message)
        //}
    });

    return result;    
*/
}



function getElementByXPath(xpath) {
  return new XPathEvaluator()
    .createExpression(xpath)
    .evaluate(document, XPathResult.FIRST_ORDERED_NODE_TYPE)
    .singleNodeValue
}

function eventFire(el, etype, callback, callbackDelay){
  if (el.fireEvent) {
    el.fireEvent('on' + etype);
  } else {
    var evObj = document.createEvent('Events');
    evObj.initEvent(etype, true, false);
    el.dispatchEvent(evObj);
  }

    //callback
    if (typeof callback === "function") {
        setTimeout(function() {
            callback(el);
        }, callbackDelay || 1);
    }

}

function simulateClick(el, callback) {

    eventFire(mySelectorAll(el)[0], 'click', callback);    
    console.log('simulateClick: '+el);
}




function writeInput(sel, value, callback, callbackDelay) 
{
    if ((typeof sel == 'string') && (sel.substr(0,2) == '//')) {

        var input = getElementByXPath(sel);
    } else {

        var input = mySelectorAll(sel)[0];
    }


    if (input instanceof HTMLDivElement)
        input.innerText = value; else
        input.value  = value;

    //---var input       = document.querySelectorAll(sel);
    //---input[0].value  = value;
    
    var event       = document.createEvent("Event");
    event.initEvent("input", true, true);
    event.isTrusted = true;
    input.dispatchEvent(event);

    var event = new Event('change');
    input.dispatchEvent(event);

    //callback
    if (typeof callback === "function") {
        setTimeout(function() {
            callback();
        }, callbackDelay || 1);
    }   

}



function waitClicked(callback, callbackDelay) {

    var int2 = 
    setInterval(function() {

        if (window.realClicked == true) {
            clearInterval(int2);

            //alert('clicked');

            //callback
            if (typeof callback === "function") {
                setTimeout(function() {
                    callback();
                }, callbackDelay || 1);
            }   

        }
    }, 100);
}


function waitFound(selector, callback, callbackDelay, execWhile) {

    console.log('waitFound: ' + selector);

    let intervalInt = mySelectorAll(selector).length == 0 ? 100 : 0;

    var int2 = 
    setInterval(function() {

        let result = mySelectorAll(selector);

        if (result.length > 0) {
            clearInterval(int2);
            console.log('waitFound: ' + selector + ' (' + result[0].classList.value + ') - YES!');

            //callback
            if (typeof callback === "function") {
                setTimeout(function() {
                    callback(result[0]);
                }, callbackDelay || 1);
            }   
        } else {
            if (typeof execWhile === "function") {
                execWhile();
            }
        }
    }, intervalInt);
}


function waitNotFound(selector, callback, callbackDelay, execWhile) {

    console.log('waitNotFound: ' + selector);

    let intervalInt = mySelectorAll(selector).length == 0 ? 0 : 100;

    var targ = mySelectorAll(selector);
    var int2 = 
    setInterval(function() {

        //console.log('waitin: '+$('[name=phone]').length+', '+targ.length);

        if (mySelectorAll(selector).length == 0) {
            clearInterval(int2);
            console.log('waitNotFound: ' + selector + ' - YES!');

            //callback
            if (typeof callback === "function") {
                setTimeout(function() {
                    callback();
                }, callbackDelay || 1);
            }   
        } else {
            if (typeof execWhile === "function") {
                execWhile();
            }
        }
    }, intervalInt);
}


//eventFire($('.tg_head_btn')[0], 'click', function() {
//});


function myOffset(el) {
    var rect = el.getBoundingClientRect(),
    scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
    scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    return { top: rect.top + scrollTop, left: rect.left + scrollLeft }
}

function sendRealClick(selector, callback, callbackDelay) {

    //alert('sendRealClick');

    if ((typeof selector == 'string') && (selector.substr(0,2) == '//')) {
        //alert(selector);
        var target = $(getElementByXPath(selector));
    } else {
        //alert('else');
        
        var target = mySelectorAll(selector)[0];
    }
    
    // var offset = target.offset();
    // var posY = offset.top - $(window).scrollTop();
    // var posX = offset.left - $(window).scrollLeft();
    
    var posY = target.offsetTop;// - $(window).scrollTop();
    var posX = target.offsetLeft;// - $(window).scrollLeft();
    
    var posY = myOffset(target).top;// - $(window).scrollTop();
    var posX = myOffset(target).left;// - $(window).scrollLeft();
    
    console.log('sendRealClick: x='+(posX+(target.offsetWidth/2))+', y='+(posY+(target.offsetHeight/2)) );
    

    window.realClicked = false;

    try {
        console.log(JSON.stringify(
            {
                href: "/click", 
                selector: selector,
                x: (posX+(target.offsetWidth/2)), 
                y: (posY+(target.offsetHeight/2))
            }
        ));
    }
    catch (err) {
        console.log(err.message);
    }

    waitClicked(function(){

        //callback
        if (typeof callback === "function") {
            var sleep1 =
            setInterval(function() {
                clearInterval(sleep1);
                callback({msg: 'clip callback!'});

            }, callbackDelay || 1);
        }   

    });


}


function setFiles(files, callback)
{
    console.log(JSON.stringify({
        href: "/files",
        files: [
            {
                name: files
            }
        ]
    }));

    callback();
}



function SetEnabled(AEnabled, ADebug = false) {
    if (!ADebug)
        console.log(JSON.stringify({
            href: "/enabled", 
            value: AEnabled,
            debug: ADebug
        }));    

    let bodyEl = mySelectorAll('body')[0];

    if (!AEnabled && !ADebug)
        bodyEl.classList.add('silent'); else
        bodyEl.classList.remove('silent');
}



console.log('common.js is INJECTED!');