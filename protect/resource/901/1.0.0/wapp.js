//---alert('will load');
/*
if (!window.Store) {
    (function() {
        function getStore(modules) {
            let foundCount = 0;
            let neededObjects = [
                { id: "Store", conditions: (module) => (module.Chat && module.Msg) ? module : null },
                { id: "Wap", conditions: (module) => (module.createGroup) ? module : null },
                { id: "MediaCollection", conditions: (module) => (module.default && module.default.prototype && module.default.prototype.processFiles !== undefined) ? module.default : null },
                { id: "WapDelete", conditions: (module) => (module.sendConversationDelete && module.sendConversationDelete.length == 2) ? module : null },
                { id: "Conn", conditions: (module) => (module.default && module.default.ref && module.default.refTTL) ? module.default : null },
                { id: "WapQuery", conditions: (module) => (module.queryExist) ? module : null },
                { id: "ProtoConstructor", conditions: (module) => (module.prototype && module.prototype.constructor.toString().indexOf('binaryProtocol deprecated version') >= 0) ? module : null },
                { id: "UserConstructor", conditions: (module) => (module.default && module.default.prototype && module.default.prototype.isServer && module.default.prototype.isUser) ? module.default : null }
            ];

            for (let idx in modules) {
                if ((typeof modules[idx] === "object") && (modules[idx] !== null)) {
                    let first = Object.values(modules[idx])[0];
                    if ((typeof first === "object") && (first.exports)) {
                        for (let idx2 in modules[idx]) {
                            let module = modules(idx2);
                            if (!module) {
                                continue;
                            }

                            neededObjects.forEach((needObj) => {
                                if(!needObj.conditions || needObj.foundedModule) return;
                                let neededModule = needObj.conditions(module);
                                if(neededModule !== null) {
                                    foundCount++;
                                    needObj.foundedModule = neededModule;
                                }
                            });

                            if(foundCount == neededObjects.length) {
                                break;
                            }
                        }

                        let neededStore = neededObjects.find((needObj) => needObj.id === "Store");
                        window.Store = neededStore.foundedModule ? neededStore.foundedModule : {};
                        neededObjects.splice(neededObjects.indexOf(neededStore), 1);
                        neededObjects.forEach((needObj) => {
                            if(needObj.foundedModule) {
                                window.Store[needObj.id] = needObj.foundedModule;
                            }
                        });

                        return window.Store;
                    }
                }
            }
        }

        webpackJsonp([], {'parasite': (x, y, z) => getStore(z)}, 'parasite');
    })();
}
*/

if (!window.Store) {
    (function () {
        function getStore(modules) {
            let foundCount = 0;
            let neededObjects = [
                {id: "Store", conditions: (module) => (module.Chat && module.Msg) ? module : null},
                /* old
                {
                    id: "MediaCollection",
                    conditions: (module) => (module.default && module.default.prototype && module.default.prototype.processFiles !== undefined) ? module.default : null
                },
                */
                { 
                    id: "MediaCollection", 
                    conditions: (module) => (module.default && module.default.prototype && module.default.prototype.processAttachments) ? module.default : null 
                },
                {
                    id: "ChatClass",
                    conditions: (module) => (module.default && module.default.prototype && module.default.prototype.Collection !== undefined && module.default.prototype.Collection === "Chat") ? module : null
                },
                {id: "MediaProcess", conditions: (module) => (module.BLOB) ? module : null},
                {id: "Wap", conditions: (module) => (module.createGroup) ? module : null},
                {
                    id: "ServiceWorker",
                    conditions: (module) => (module.default && module.default.killServiceWorker) ? module : null
                },
                {id: "State", conditions: (module) => (module.STATE && module.STREAM) ? module : null},
                {
                    id: "WapDelete",
                    conditions: (module) => (module.sendConversationDelete && module.sendConversationDelete.length == 2) ? module : null
                },
                {
                    id: "Conn",
                    conditions: (module) => (module.default && module.default.ref && module.default.refTTL) ? module.default : null
                },
                {
                    id: "WapQuery",
                    conditions: (module) => (module.queryExist) ? module : ((module.default && module.default.queryExist) ? module.default : null)
                },
                {id: "CryptoLib", conditions: (module) => (module.decryptE2EMedia) ? module : null},
                {
                    id: "OpenChat",
                    conditions: (module) => (module.default && module.default.prototype && module.default.prototype.openChat) ? module.default : null
                },
                {
                    id: "UserConstructor",
                    conditions: (module) => (module.default && module.default.prototype && module.default.prototype.isServer && module.default.prototype.isUser) ? module.default : null
                },
                {
                    id: "SendTextMsgToChat",
                    conditions: (module) => (module.sendTextMsgToChat) ? module.sendTextMsgToChat : null
                },
                {  
                    id: "SendSeen",
                    conditions: (module) => (module.sdendSeen) ? module.sendSeen : null
                },
            ];
            for (let idx in modules) {
                if ((typeof modules[idx] === "object") && (modules[idx] !== null)) {
                    let first = Object.values(modules[idx])[0];
                    if ((typeof first === "object") && (first.exports)) {
                        for (let idx2 in modules[idx]) {
                            let module = modules(idx2);
                            if (!module) {
                                continue;
                            }
                            neededObjects.forEach((needObj) => {
                                if (!needObj.conditions || needObj.foundedModule)
                                    return;
                                let neededModule = needObj.conditions(module);
                                if (neededModule !== null) {
                                    foundCount++;
                                    needObj.foundedModule = neededModule;
                                }
                            });
                            if (foundCount == neededObjects.length) {
                                break;
                            }
                        }

                        let neededStore = neededObjects.find((needObj) => needObj.id === "Store");
                        window.Store = neededStore.foundedModule ? neededStore.foundedModule : {};
                        neededObjects.splice(neededObjects.indexOf(neededStore), 1);
                        neededObjects.forEach((needObj) => {
                            if (needObj.foundedModule) {
                                window.Store[needObj.id] = needObj.foundedModule;
                            }
                        });
                        
                        try {

                            window.Store.sendMessage = function (e) {
                            //window.Store.ChatClass.default.prototype.sendMessage = function (e) {
                                return window.Store.SendTextMsgToChat(this, ...arguments);
                            }

                        } catch(err) {
                          console.log('wapp.js ERROR ---=> '+err.message);
                        }

                        return window.Store;
                    }
                }
            }
        }

        //webpackJsonp([], {'parasite': (x, y, z) => getStore(z)}, ['parasite']);

        if (typeof webpackJsonp === 'function') {
            console.log('IF!!!');
            webpackJsonp([], {'parasite': (x, y, z) => getStore(z)}, ['parasite']);
        } else {
            console.log('ELSE!!!');
            webpackJsonp.push([
                ['parasite'],
                {
                    parasite: function (o, e, t) {
                        getStore(t);
                    }
                },
                [['parasite']]
            ]);
        }

    })();
}

/*start gambiarra, corrigir o fado de ter sumido WApQuery*/
//window.Store.WapQuery = {}; webpackJsonp([], { "dgfhfgbdeb": (x, y, z) => window.Store.WapQuery = z('"dgfhfgbdeb"') }, "dgfhfgbdeb");
//window.Store.WapQuery = window.Store.WapQuery.default;
/*end gambiarra*/

window.WAPI = {
    lastRead: {}
};

window.WAPI._serializeRawObj = (obj) => {
    if (obj) {
        return obj.toJSON();
    }
    return {}
};


/**
 * Serializes a chat object
 *
 * @param rawChat Chat object
 * @returns {{}}
 */


window.WAPI._serializeChatObj = (obj) => {
    if (obj == undefined) {
        return null;
    }

    return Object.assign(window.WAPI._serializeRawObj(obj), {
        kind: obj.kind,
        isGroup: obj.isGroup,
        contact: obj['contact'] ? window.WAPI._serializeContactObj(obj['contact']) : null,
        groupMetadata: obj["groupMetadata"] ? window.WAPI._serializeRawObj(obj["groupMetadata"]) : null,
        presence: obj["presence"] ? window.WAPI._serializeRawObj(obj["presence"]) : null,
        msgs: null
    });
};

window.WAPI._serializeContactObj = (obj) => {
    if (obj == undefined) {
        return null;
    }

    return Object.assign(window.WAPI._serializeRawObj(obj), {
        formattedName: obj.formattedName,
        isHighLevelVerified: obj.isHighLevelVerified,
        isMe: obj.isMe,
        isMyContact: obj.isMyContact,
        isPSA: obj.isPSA,
        isUser: obj.isUser,
        isVerified: obj.isVerified,
        isWAContact: obj.isWAContact,
        profilePicThumbObj: obj.profilePicThumb ? WAPI._serializeProfilePicThumb(obj.profilePicThumb) : {},
        statusMute: obj.statusMute,
        msgs: null
    });
};

window.WAPI._serializeMessageObj = (obj) => {
    if (obj == undefined) {
        return null;
    }

    return Object.assign(window.WAPI._serializeRawObj(obj), {
        id: obj.id.id, 
        cid: obj['cid'],
        sender: obj["senderObj"] ? WAPI._serializeContactObj(obj["senderObj"]) : null,
        timestamp: obj["t"],
        content: obj["body"],
        isGroupMsg: obj.isGroupMsg,
        isLink: obj.isLink,
        isMMS: obj.isMMS,
        isMedia: obj.isMedia,
        isNotification: obj.isNotification,
        isPSA: obj.isPSA,
        type: obj.type,
        chat: WAPI._serializeChatObj(obj['chat']),
        chatId: obj.id.remote,
        quotedMsgObj: WAPI._serializeMessageObj(obj['_quotedMsgObj']),
        mediaData: window.WAPI._serializeRawObj(obj['mediaData']),
    });
};

window.WAPI._serializeNumberStatusObj = (obj) => {
    if (obj == undefined) {
        return null;
    }

    return Object.assign({}, {
        id: obj.jid,
        status: obj.status,
        isBusiness: (obj.biz === true),
        canReceiveMessage: (obj.status === 200)
    });
};

window.WAPI._serializeProfilePicThumb = (obj) => {
    if (obj == undefined) {
        return null;
    }

    return Object.assign({}, {
        eurl: obj.eurl,
        id: obj.id,
        img: obj.img,
        imgFull: obj.imgFull,
        raw: obj.raw,
        tag: obj.tag
    });
}


/**
 * FUNCTIONS
 *
 *
 *
 */

window.WAPI.getChat = function (id, done) {
    id = typeof id == "string" ? id : id._serialized;
    const found = window.Store.Chat.get(id);
    if (done !== undefined) {
        done(found);
    } else {
        return found;
    }
};

window.WAPI.checkNumberStatus = function(id, done) {
    window.Store.WapQuery.queryExist(id).then((result) => {
        if(done !== undefined) {
            done(window.WAPI._serializeNumberStatusObj(result));
        }
    }).catch(() => {
        if(done !== undefined) {
            done(window.WAPI._serializeNumberStatusObj({
                status: 500,
                jid: id
            }));
        }
    });

    return true;
};

window.WAPI.base64ImageToFile = function (b64Data, filename) {
    var arr = b64Data.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, {type: mime});
};

/**
 * Fetches all contact objects from store, filters them
 *
 * @param done Optional callback function for async execution
 * @returns {Array|*} List of contacts
 */
window.WAPI.getMyContacts = function (done) {
    const contacts = window.Store.Contact.filter((contact) => contact.isMyContact === true).map((contact) => WAPI._serializeContactObj(contact));

    if (done !== undefined) {
        done(contacts);
    } else {
        return contacts;
    }
};

/**
 * Fetches all groups objects from store
 *
 * @param done Optional callback function for async execution
 * @returns {Array|*} List of chats
 */
window.WAPI.getAllGroups = function (done) {
    const groups = window.Store.Chat.filter((chat) => chat.isGroup);

    if (done !== undefined) {
        done(groups);
    } else {
        return groups;
    }
};


/**
 * Fetches group metadata object from store by ID
 *
 * @param id ID of group
 * @param done Optional callback function for async execution
 * @returns {T|*} Group metadata object
 */
window.WAPI.getGroupMetadata = async function (id, done) {
    let output = window.Store.GroupMetadata.get(id);

    if (output !== undefined) {
        if (output.stale) {
            await output.update();
        }
    }

    if (done !== undefined) {
        done(output);
    }
    return output;

};


/**
 * Fetches group participants
 *
 * @param id ID of group
 * @returns {Promise.<*>} Yields group metadata
 * @private
 */
window.WAPI._getGroupParticipants = async function (id) {
    const metadata = await WAPI.getGroupMetadata(id);
    return metadata.participants;
};

/*
window.WAPI.getProfilePicSmallFromId = function(id, done) {
    window.Store.ProfilePicThumb.find(id).then(function(d) {
        if(d.img !== undefined) {
            window.WAPI.downloadFileWithCredentials(d.img, done);
        } else {
            done(false);
        }
    })
};
*/

window.WAPI.downloadFileWithCredentials = function (url, done) {
    let xhr = new XMLHttpRequest();


    xhr.onload = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                let reader = new FileReader();
                reader.readAsDataURL(xhr.response);
                reader.onload = function (e) {
                    done(reader.result.substr(reader.result.indexOf(',') + 1))
                };
            } else {
                console.error(xhr.statusText);
            }
        } else {
            console.log(err);
            done(false);
        }
    };

    xhr.open("GET", url, true);
    xhr.withCredentials = true;
    xhr.responseType = 'blob';
    xhr.send(null);
};


window.WAPI.imageToDataUri = function (uri, width, height, done)
{
    var img    = new Image;
    img.src    = uri;
    img.onload =
                function ()
                {
                    var canvas    = document.createElement('canvas'),
                        ctx       = canvas.getContext('2d');

                    canvas.width  = width;
                    canvas.height = height;

                    //ctx.drawImage(img, 0, 0, width, height);
                    ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, width, height);

                    done(canvas.toDataURL());
                }
}

console.log(JSON.stringify({
    'href': '/inject',
    'module': 'WAPI 5.5.5',
}));


//alert('loaded');