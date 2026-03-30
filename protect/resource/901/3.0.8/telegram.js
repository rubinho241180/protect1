WAPP = {};

BTN_MESSAGE_NEW   = ".NewChatButton .MenuItem:last-child";
BTN_CONTACT_NEW   = '[title="Create New Contact"]';

INP_CONTACT_PHONE = '[aria-label="Phone Number"]';
INP_CONTACT_FNAME = '[aria-label="First name (required)"]';
BTN_CONTACT_SAVE  = '.dialog-buttons button:last-child';

DIV_MESSAGES      = '.messages-layout>.Transition>.Transition_slide-active';

INP_MESSAGE_LOCK  = DIV_MESSAGES + ' #editable-message-text[contenteditable=false]';
INP_MESSAGE_TEXT  = DIV_MESSAGES + ' #editable-message-text[contenteditable=true]';
BTN_MESSAGE_SEND  = DIV_MESSAGES + ' [title="Send Message"]';

BTN_ATTACH_MENU   = '#attach-menu-button';
BTN_ATTACH_PHOTO  = DIV_MESSAGES + ' #attach-menu-controls .icon-photo';
INP_ATTACH_TEXT   = '#editable-message-text-modal[contenteditable=true]';
BTN_ATTACH_SEND   = '#caption-input-text+div button';



RETURN_WHILE    = '[title="Return to chat list"],.MiddleHeader h3';


BTN_MESSAGE_SEND  = '[title="Send Message"],button.default.primary:not(.round)';
BTN_ATTACH_SENDC  = '[title="Send Message"],button.default.primary:not(.round)';


MSGS_LAST_ITEM    = '.Transition_slide-active>.MessageList .last-in-list';
MSGS_LAST_ACTIVE  = MSGS_LAST_ITEM +  ' .Transition_slide-active';

function openChat(phone, events)
{
    mySelectorAll(DIV_MESSAGES+' #message-input-text').forEach((e) => {
        e.classList.add('ignore');
    });

    var jobOpen = 
    {
        whileFoundSendKeys: RETURN_WHILE, delay: 100, then: 
        {
            clickTry: BTN_MESSAGE_NEW, then: 
            {
                click: BTN_CONTACT_NEW, delay: 100, then:
                {
                    writeInput: INP_CONTACT_PHONE, value: phone, then: 
                    {
                        writeInput: INP_CONTACT_FNAME, value: 'My '+phone, then: 
                        {
                            click: BTN_CONTACT_SAVE, then:
                            {
                                waitFound: DIV_MESSAGES+' #message-input-text:not(.ignore),'+'.Notification:not(.ignore)', then: () => 
                                {
                                    let notifications = mySelectorAll('.Notification:not(.ignore)');
                                    
                                    if (notifications.length > 0) {
                                        
                                        notifications.forEach((el) => {
                                            el.innerText = el.innerText + ' (IGNORED)';
                                            el.classList.add('ignore');
                                        });
                                        
                                        sendRealClick('.NewContactModal button:first-child', () => {
                                            whileFoundSendKeys(RETURN_WHILE);
                                        });


                                        events.notFound();
                                    } else {
                                        events.found();
                                    }
                                }
                            }    
                        }
                    }
                }
            }
        }
    }

    JOBS.run(jobOpen);
}





function unlockChat(events)
{
    let jobUnlock =
    {
        click: '.MiddleHeader h3', delay: 100, then: 
        {
            //click: '.right-column-open .animated-close-icon:not(.state-back)', delay: 10, then:
            //sendKeys: ['ESC'], delay: 10, then:
            whileFoundSendKeys: '.right-column-open', then: 
            {
                //waitNotFound: '.right-column-open', then:
                //{
                    waitNotFound: INP_MESSAGE_LOCK, then: () =>
                    {
                        events.unlocked();
                    }
                //}
            }
        }
    };    

    setTimeout(() => { 
        JOBS.run(jobUnlock); 
    }, 1000);
}





function sendText(MSG, events)
{
    let jobText =
    {
        writeInput: INP_MESSAGE_TEXT, value: decodeURIComponent(MSG.text.body), then:
        {
            click: BTN_MESSAGE_SEND, delay: 1000, then: 
            {  
                //wait send status  
                waitFound: MSGS_LAST_ACTIVE+' .icon-message-succeeded,' +MSGS_LAST_ACTIVE+' .icon-message-failed', then: () =>
                {
                    var succeeded = mySelectorAll(MSGS_LAST_ACTIVE+' .icon-message-succeeded');

                    if (succeeded.length > 0) 
                        events.sent(); else
                        events.fail();

                    //whileFoundSendKeys('[title="Return to chat list"]');
                }
            }
        }
    }; 

    //setTimeout(() => { 
        JOBS.run(jobText); 
    //}, 250);
}





function sendFile(FILE, events)
{
    var jobFile = 
    {
        writeInput: INP_MESSAGE_TEXT, value: decodeURIComponent(FILE.caption), then:
        {
            files: [decodeURIComponent(FILE.filePath)], then:
            {
                click: BTN_ATTACH_MENU, then:
                {
                    click: BTN_ATTACH_PHOTO, then: 
                    {
                        click: BTN_ATTACH_SEND, delay: 2000, then: 
                        {
                            //waitFound: '.last-in-list .MessageOutgoingStatus .Transition_slide', len: 2, then:
                            //waitFound: MSGS_LAST_ACTIVE+' .icon-message-pending', then:
                            //{
                                //wait send status  
                                waitFound: MSGS_LAST_ACTIVE+' .icon-message-succeeded,' +MSGS_LAST_ACTIVE+' .icon-message-failed', then: () =>
                                {
                                    var succeeded = mySelectorAll(MSGS_LAST_ACTIVE+' .icon-message-succeeded');

                                    if (succeeded.length > 0) 
                                        events.sent(); else
                                        events.fail();

                                    //whileFoundSendKeys('[title="Return to chat list"]');
                                }
                            //}
                        }    
                    }
                }
            }
        }
    }

    JOBS.run(jobFile); 
}





WAPP.sendBatch = function(SND)
{
    SetEnabled(false, SND.debug);

    openChat(SND.batch.target.address, {
        found: () => {

            //OPENNED
            unlockChat({
                unlocked: () => {
                    
                    //UNLOCKED
                    if (SND.batch.messages[0].files.length == 0)
                        
                        //TEXT
                        sendText(SND.batch.messages[0], {
                            sent: () => {
                                //SENT
                                SND.status = 200;
                                console.log(JSON.stringify(SND));  
                                SetEnabled(true);
                            },
                            fail: () => {
                                //FAIL
                                SND.status = 500;
                                console.log(JSON.stringify(SND));  
                                SetEnabled(true);
                            }
                        }); else
                        
                        //FILE
                        sendFile(SND.batch.messages[0].files[0], 
                        {
                            sent: () => {
                                //SENT
                                SND.status = 200;
                                console.log(JSON.stringify(SND));  
                                SetEnabled(true);
                            },
                            fail: () => {
                                //FAIL
                                SND.status = 500;
                                console.log(JSON.stringify(SND));  
                                SetEnabled(true);
                            }
                        });
                }
            })
        },

        notFound: () => {
            SND.status = 404;
            console.log(JSON.stringify(SND));  
            SetEnabled(true);
        }
    });
}



console.log('telegram.js INJECTED!');
