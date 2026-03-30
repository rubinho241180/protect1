if (!window.Store||!window.Store.Msg) {
    (function () {
        function getStore(modules) {
            let foundCount = 0;
            let neededObjects = [
                { id: "Store", conditions: (module) => (module.default && module.default.Chat && module.default.Msg) ? module.default : null},
  				{ id: "Conn", conditions: (module) => (module.default && module.default.ref && module.default.refTTL) ? module.default : (module.Conn ? module.Conn : null)},
                { id: "MediaCollection", conditions: (module) => (module.default && module.default.prototype && (module.default.prototype.processFiles !== undefined||module.default.prototype.processAttachments !== undefined)) ? module.default : null },
                { id: "MediaProcess", conditions: (module) => (module.BLOB) ? module : null },
                { id: "Archive", conditions: (module) => (module.setArchive) ? module : null },
                { id: "Block", conditions: (module) => (module.blockContact && module.unblockContact) ? module : null },
                { id: "ChatUtil", conditions: (module) => (module.sendClear) ? module : null },
				{ id: "GroupInvite", conditions: (module) => (module.sendQueryGroupInviteCode ) ? module : null },
                { id: "Wap", conditions: (module) => (module.createGroup) ? module : null },
                { id: "ServiceWorker", conditions: (module) => (module.default && module.default.killServiceWorker) ? module : null },
                { id: "State", conditions: (module) => (module.STATE && module.STREAM) ? module : null },
                { id: "_Presence", conditions: (module) => (module.setPresenceAvailable && module.setPresenceUnavailable) ? module : null },
                { id: "WapDelete", conditions: (module) => (module.sendConversationDelete && module.sendConversationDelete.length == 2) ? module : null },
                { id: 'FindChat', conditions: (module) => (module && module.findChat) ? module : null},				
		//{ id: "WapQuery", conditions: (module) => (module.default && module.default.queryExist) ? module.default : null },
		{ id: "WapQuery", conditions: (module) => (module.queryExist) ? module : ((module.default && module.default.queryExist) ? module.default : null) },
		{ id: "WapQueryMD", conditions: (module) => (module.queryPhoneExists && module) ? module : null },	
	        { id: 'Perfil', conditions: (module) => module.__esModule === true && module.setPushname && !module.getComposeContents ? module : null},		
                { id: "OpenChat", conditions: (module) => (module.default && module.default.prototype && module.default.prototype.openChat) ? module.default : null },
                { id: "UserConstructor", conditions: (module) => (module.default && module.default.prototype && module.default.prototype.isServer && module.default.prototype.isUser) ? module.default : null },
                { id: "SendTextMsgToChat", conditions: (module) => (module.sendTextMsgToChat) ? module.sendTextMsgToChat : null },
                { id: "ReadSeen", conditions: (module) => (module.sendSeen) ? module : null },
                { id: "sendDelete", conditions: (module) => (module.sendDelete) ? module.sendDelete : null },
                { id: "addAndSendMsgToChat", conditions: (module) => (module.addAndSendMsgToChat) ? module.addAndSendMsgToChat : null },
                { id: "sendMsgToChat", conditions: (module) => (module.sendMsgToChat) ? module.sendMsgToChat : null },
                { id: "Catalog", conditions: (module) => (module.Catalog) ? module.Catalog : null },
                { id: "bp", conditions: (module) => (module.default&&module.default.toString&&module.default.toString().includes('bp_unknown_version')) ? module.default : null },
                { id: "MsgKey", conditions: (module) => (module.default&&module.default.toString&&module.default.toString().includes('MsgKey error: obj is null/undefined')) ? module.default : null },
                { id: "Parser", conditions: (module) => (module.convertToTextWithoutSpecialEmojis) ? module.default : null },
                { id: "Builders", conditions: (module) => (module.TemplateMessage && module.HydratedFourRowTemplate) ? module : null },
                { id: "Me", conditions: (module) => (module.PLATFORMS && module.Conn) ? module.default : null },
                { id: "CallUtils", conditions: (module) => (module.sendCallEnd && module.parseCall) ? module : null },
                { id: "Identity", conditions: (module) => (module.queryIdentity && module.updateIdentity) ? module : null },
                { id: "MyStatus", conditions: (module) => (module.getStatus && module.setMyStatus) ? module : null },                
				{ id: "ChatStates", conditions: (module) => (module.sendChatStatePaused && module.sendChatStateRecording && module.sendChatStateComposing) ? module : null },				
                { id: "GroupActions", conditions: (module) => (module.sendExitGroup && module.localExitGroup) ? module : null },
                { id: "Features", conditions: (module) => (module.FEATURE_CHANGE_EVENT && module.features) ? module : null },
                { id: "MessageUtils", conditions: (module) => (module.storeMessages && module.appendMessage) ? module : null },
                { id: "WebMessageInfo", conditions: (module) => (module.WebMessageInfo && module.WebFeatures) ? module.WebMessageInfo : null },
                { id: "createMessageKey", conditions: (module) => (module.createMessageKey && module.createDeviceSentMessage) ? module.createMessageKey : null },
                { id: "Participants", conditions: (module) => (module.addParticipants && module.removeParticipants && module.promoteParticipants && module.demoteParticipants) ? module : null },
                { id: "WidFactory", conditions: (module) => (module.isWidlike && module.createWid && module.createWidFromWidLike) ? module : null },
                { id: "Base", conditions: (module) => (module.setSubProtocol && module.binSend && module.actionNode) ? module : null },
   				{ id: "Versions", conditions: (module) => (module.loadProtoVersions && module.default && module.default["15"] && module.default["16"] && module.default["17"]) ? module : null },
		        { id: "Sticker", conditions: (module) => (module.default && module.default.Sticker) ? module.default.Sticker : null },
                { id: "MediaUpload", conditions: (module) => (module.default && module.default.mediaUpload) ? module.default : null },
                { id: "UploadUtils", conditions: (module) => (module.default && module.default.encryptAndUpload) ? module.default : null },
				{ id: 'UserPrefs', conditions: (module) => (module.getMaybeMeUser ? module : null), },
                { id: 'Vcard', conditions: (module) => (module.vcardFromContactModel ? module : null)}
            ];
            for (let idx in modules) {
            	if ((typeof modules[idx] === "object") && (modules[idx] !== null)) {
                    neededObjects.forEach((needObj) => {
                    	if (!needObj.conditions || needObj.foundedModule)
                            return;
                    	let neededModule = needObj.conditions(modules[idx]);
                    	if (neededModule !== null) {
                            foundCount++;
                            needObj.foundedModule = neededModule;
                    	}
		    });

                    if (foundCount == neededObjects.length) {
                    	break;
                    }
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
	    window.Store.Chat.modelClass.prototype.sendMessage = function (e) {
		window.Store.SendTextMsgToChat(this, ...arguments);
	    }
            return window.Store;
    	}
        const parasite = `parasite${Date.now()}`

        if (typeof webpackJsonp === 'function') webpackJsonp([], {[parasite]: (x, y, z) => getStore(z)}, [parasite]); 
		else webpackChunkwhatsapp_web_client.push([[parasite], {}, function (o, e, t) {let modules = []; for (let idx in o.m) {modules.push(o(idx));}	getStore(modules);}]);
        
    })();
}


window.WAPI = {};
window._WAPI = {};

window.WAPI._serializeRawObj = (obj) => {
    if (obj && obj.toJSON) {
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
        formattedTitle: obj.formattedTitle,
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
	
	let profilePhoto = window.Store.ProfilePicThumb._index[obj.__x_id._serialized] ? window.Store.ProfilePicThumb._index[obj.__x_id._serialized].__x_imgFull : {}
	
    return Object.assign(window.WAPI._serializeRawObj(obj), {
		id: obj.id._serialized,
        formattedName: obj.formattedName,
        isHighLevelVerified: obj.isHighLevelVerified,
        isMe: obj.isMe,
        //isMyContact: obj.isMyContact,
		isMyContact: (obj.isAddressBookContact === 1) ? true : false,
		isAddressBookContact: obj.isAddressBookContact,
        isPSA: obj.isPSA,
        isUser: obj.isUser,
        isVerified: obj.isVerified,
        isWAContact: obj.isWAContact,		
        profilePicThumb: profilePhoto,
		statusMute: obj.statusMute,
        msgs: null
    });
};

window.WAPI._serializeMessageObj = (obj) => {
    if (obj == undefined) {
        return null;
    }
    const _chat = obj['chat'] ? WAPI._serializeChatObj(obj['chat']) : {};
    return Object.assign(window.WAPI._serializeRawObj(obj), {
        id: obj.id._serialized,
        //add 02/06/2020 mike -->
		quotedParticipant: obj.quotedParticipant? obj.quotedParticipant._serialized ? obj.quotedParticipant._serialized : undefined : undefined,
        author: obj.author? obj.author._serialized ? obj.author._serialized : undefined : undefined,
        chatId: obj.chatId? obj.chatId._serialized ? obj.chatId._serialized : undefined : undefined,
        to: obj.to? obj.to._serialized ? obj.to._serialized : undefined : undefined,
        fromMe: obj.id.fromMe,
		//add 02/06/2020 mike <--
		
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
        chat: _chat,
        isOnline: _chat.isOnline,
        lastSeen: _chat.lastSeen,
        chatId: obj.id.remote,
	//quotedMsgObj: WAPI._serializeMessageObj(obj['_quotedMsgObj']),
        quotedMsgObj: WAPI._serializeMessageObj(WPP.whatsapp.functions.getQuotedMsgObj(obj)),
	//quotedMsgObj: null,
        mediaData: window.WAPI._serializeRawObj(obj['mediaData']),
        reply: body => window.WAPI.reply(_chat.id._serialized, body, obj)
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

window.WAPI._serializeNumberStatusObjMD = (obj) => {
    if (obj == undefined) {
        return null;
    }

	let awid = false

	var	_awid = ""+obj.wid+""

	if (_awid.length > 0){
		awid = true
	} else {
		awid = false
	}

	console.log('_awid: '+ awid)

    return Object.assign({}, {
        id: obj.wid,
        status: awid	
        //isBusiness: (obj.biz === true)
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
        //imgFull: obj.imgFull,
		imgFull: obj.__x_imgFull,
        raw: obj.raw,
        tag: obj.tag
    });
}
