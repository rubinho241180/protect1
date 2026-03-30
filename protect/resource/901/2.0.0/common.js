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
    console.log('iniToObj: '+url);
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

    eventFire($(el)[0], 'click', callback);    
}




function writeInput(sel, value, callback, callbackDelay) 
{
    if ((typeof sel == 'string') && (sel.substr(0,2) == '//')) {

        var input = getElementByXPath(sel);
    } else {

        var input = $(sel)[0];
    }

    input.value  = value;

    //---var input       = document.querySelectorAll(sel);
    //---input[0].value  = value;
    
    var event       = document.createEvent("Event");
    event.initEvent("input", true, true);
    event.isTrusted = true;
    jQuery(input)[0].dispatchEvent(event);

    var event = new Event('change');
    jQuery(input)[0].dispatchEvent(event);

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


function waitFound(selector, callback, callbackDelay) {

    var int2 = 
    setInterval(function() {

        if ($(selector).length > 0) {
            clearInterval(int2);

            //callback
            if (typeof callback === "function") {
                setTimeout(function() {
                    callback($(selector));
                }, callbackDelay || 1);
            }   

        }
    }, 100);
}


function waitNotFound(selector, callback, callbackDelay) {

    var targ = $(selector);
    var int2 = 
    setInterval(function() {

        //console.log('waitin: '+$('[name=phone]').length+', '+targ.length);

        if ($(selector).length == 0) {
            clearInterval(int2);

            //callback
            if (typeof callback === "function") {
                setTimeout(function() {
                    callback();
                }, callbackDelay || 1);
            }   
        }
    }, 100);
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
        
        var target = $(selector);
    }
    
    var offset = target.offset();
    var posY = offset.top - $(window).scrollTop();
    var posX = offset.left - $(window).scrollLeft();
    
    
    /*
    var div = document.querySelector(selector);
    var divOffset = myOffset(div);
    
    var posY = divOffset.top;
    var posX = divOffset.left;
    */
    

    window.realClicked = false;

    console.log(JSON.stringify(
        {
            href: "/click", 
            selector: selector,
            x: (posX+(target.width()/2)), 
            y: (posY+(target.height()/2))
        }
    ));

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



console.log('common.js is INJECTED!');