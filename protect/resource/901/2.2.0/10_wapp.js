//Base64 = {_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(r){var t,e,o,a,h,n,c,d="",C=0;for(r=Base64._utf8_encode(r);C<r.length;)a=(t=r.charCodeAt(C++))>>2,h=(3&t)<<4|(e=r.charCodeAt(C++))>>4,n=(15&e)<<2|(o=r.charCodeAt(C++))>>6,c=63&o,isNaN(e)?n=c=64:isNaN(o)&&(c=64),d=d+this._keyStr.charAt(a)+this._keyStr.charAt(h)+this._keyStr.charAt(n)+this._keyStr.charAt(c);return d},decode:function(r){var t,e,o,a,h,n,c="",d=0;for(r=r.replace(/[^A-Za-z0-9\+\/\=]/g,"");d<r.length;)t=this._keyStr.indexOf(r.charAt(d++))<<2|(a=this._keyStr.indexOf(r.charAt(d++)))>>4,e=(15&a)<<4|(h=this._keyStr.indexOf(r.charAt(d++)))>>2,o=(3&h)<<6|(n=this._keyStr.indexOf(r.charAt(d++))),c+=String.fromCharCode(t),64!=h&&(c+=String.fromCharCode(e)),64!=n&&(c+=String.fromCharCode(o));return c=Base64._utf8_decode(c)},_utf8_encode:function(r){r=r.replace(/\r\n/g,"\n");for(var t="",e=0;e<r.length;e++){var o=r.charCodeAt(e);o<128?t+=String.fromCharCode(o):o>127&&o<2048?(t+=String.fromCharCode(o>>6|192),t+=String.fromCharCode(63&o|128)):(t+=String.fromCharCode(o>>12|224),t+=String.fromCharCode(o>>6&63|128),t+=String.fromCharCode(63&o|128))}return t},_utf8_decode:function(r){for(var t="",e=0,o=c1=c2=0;e<r.length;)(o=r.charCodeAt(e))<128?(t+=String.fromCharCode(o),e++):o>191&&o<224?(c2=r.charCodeAt(e+1),t+=String.fromCharCode((31&o)<<6|63&c2),e+=2):(c2=r.charCodeAt(e+1),c3=r.charCodeAt(e+2),t+=String.fromCharCode((15&o)<<12|(63&c2)<<6|63&c3),e+=3);return t}};


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

window.WAPP = {
    __conn: function()
    {
        /*if (!Store.Conn2)
        {
            webpackJsonp([], { "jfefjijii": (x, y, z) => window.Store.Conn2 = z('"jfefjijii"') }, "jfefjijii");
            console.log('Store.Conn2 is INJECTED!');
            Object.keys(Store.Conn2.default).forEach(function(item){console.log('---=> '+item)});

        }

        return Store.Conn2.default;*/
        return Store.Conn;
    },
    __status: function()
    {
        /*if (!Store.Stream2)
        {
            webpackJsonp([], { "djddhaidag": (x, y, z) => window.Store.Stream2 = z('"djddhaidag"') }, "djddhaidag");
            console.log('Store.Stream2 is INJECTED!');
            Object.keys(Store.Stream2.default).forEach(function(item){console.log('---=> '+item)});
        }

        return Store.Stream2.default;*/
        //return Store.Status._listeningTo.l6;

        //pega sempre o último property (o whatsapp sempre muda o nome, por isso, deixei dinâmico)
        var v_keys = Store.Status._listeningTo;
        var l_keys = Object.values(v_keys)[Object.values(v_keys).length-1];
        return l_keys;
    },
    __meId____: function()
    {
        return this.__conn().__x_me;    
    },
    __isBeta: function() {
        return (!!localStorage.getItem('last-wid-md'));
    },
    __meNumber: function() 
    {
        if (!!localStorage.getItem('last-wid-md')) {

            return localStorage.getItem('last-wid-md').replace('"', '').split(':')[0];
        } else {
            return localStorage.getItem('last-wid').replace('"', '').split('@')[0];
        }
    },
    __meId: function() 
    {
        return this.__meNumber() + '@c.us';
    },
    __meProfile: function()
    {
        //return this.__conn();    
        return Store.Me;    
    },
    __mePicture: function()
    {
        return Store.ProfilePicThumb.get(this.__meId()._serialized);
        //return Store.Contact.get(Store.Conn.me).__x_profilePicThumb;    
    },
    __device: function()
    {
        //return this.__conn().__x_phone;
        //return Store.Me.__x_phone || {device_model: "Mdl", device_manufacturer: "Mnf"};
        return {device_model: "Mdl", device_manufacturer: "Mnf"};
    },
    stream: {
        info: 'UNKNOWN',
        observables: ['NORMAL', 'TIMEOUT', 'OFFLINE'],
    },
    status: {
        mode: 'QR1',
        simplified: 'stUnknown',
    },
    device: {
        monitoring: false,
    },
    profile: {
        id: null,
        name: null,
        picture: null,
        monitoring: false,
    },
    picture: {
        monitoring: false,
    },
};


WAPP.getProfilePicture = function(id)
{
    try
    {
        var newPict = Store.ProfilePicThumb.get(id).__x_img;

        if (newPict !=  undefined)
        {
            WAPI.imageToDataUri(newPict, 28, 28, function(uri)
                {
                    console.log(JSON.stringify(
                        {
                            href: '/picture',
                            isMe: id == WAPP.__meId()._serialized,
                            id:   id,
                            uri:  uri.split(',')[1],
                        }
                    ));
                }
            );
        } else
        {
            console.log(JSON.stringify(
                {
                    href: '/picture',
                    isMe: id == WAPP.__meId()._serialized,
                    id:   id,
                    uri:  '',
                }
            ));
        }
    }
    catch(err)
    {
        console.log('the profile >'+id+'< is not ready yet: '+err.message);
    }
}



WAPP.myCheckNumber = function(id, done) {

    window.Store.WapQuery.queryExist(id).then((result) => {
        console.log('cc:: '+ JSON.stringify(result));
        if (done !== undefined) {
    
                if (result.status == 200) {
                //if (!!result) {
                    done({
                        status: 200,
                        id: result.id,
                        jid: result.jid,
                        isBusiness: false,
                        canReceiveMessage: true
                    });
                } else {
                    done({
                        status: 404,
                        canReceiveMessage: false
                    });
                }
            }
    })
}

WAPP.myCheckNumberMD = function(id, done) {

    window.Store.WapQueryMD.queryPhoneExists('+'+id).then((result) => {
        console.log('cc:: '+ JSON.stringify(result));
        if (done !== undefined) {
    
                //if (result.status == 200) {
                if (!!result) {
                //if (!!result._value) {
                    done({
                        status: 200,
                        id: result.wid.user,
                        jid:result.wid._serialized,
                        isBusiness: false,
                        canReceiveMessage: true
                    });
                } else {
                    done({
                        status: 404,
                        canReceiveMessage: false
                    });
                }
            }
    })
}

WAPP.myCheckNumberBetaOld = function (id, done) {
    
    Store.checkNumberBeta(id)
        .then((result) => {

            if (done !== undefined) {
    
                if (!!result) {
                    done({
                        status: 200,
                        id: result.wid.user,
                        jid: result.wid._serialized,
                        isBusiness: false,
                        canReceiveMessage: true
                    });
                } else {
                    done({
                        status: 404,
                        canReceiveMessage: false
                    });
                }
            }

        })
}

WAPP.checkNumberStatus = function(id, done) {

    if (WAPP.__isBeta()) 
        WAPP.myCheckNumberMD(id, done); else 
        WAPP.myCheckNumber(id, done);
}


console.log('---=> 10_wapp.js 2.1.3');