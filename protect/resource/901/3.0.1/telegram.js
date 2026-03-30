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

BTN_ATTACH_PHOTO  = DIV_MESSAGES + ' #attach-menu-controls .icon-photo';
INP_ATTACH_TEXT   = '#editable-message-text-modal[contenteditable=true]';
BTN_ATTACH_SEND   = '#caption-input-text+div button';



function openChat(phone, events)
{
    mySelectorAll(DIV_MESSAGES+' #message-input-text').forEach((e) => {
        e.classList.add('ignore');
    });

    var jobOpen = 
    {
        clickTry: BTN_MESSAGE_NEW, then: 
        {
            click: BTN_CONTACT_NEW, then:
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
                                    
                                    sendRealClick('.NewContactModal button:first-child');
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

    JOBS.run(jobOpen);
}





function unlockChat(events)
{
    let jobUnlock =
    {
        click: '.MiddleHeader', delay: 100, then: 
        {
            click: '.right-column-open .animated-close-icon:not(.state-back)', delay: 10, then:
            {
                waitNotFound: '.right-column-open', then:
                {
                    waitNotFound: INP_MESSAGE_LOCK, then: () =>
                    {
                        events.unlocked();
                    }
                }
            }
        }
    };    

    setTimeout(() => { 
        JOBS.run(jobUnlock); 
    }, 250);
}





function sendText(MSG, events)
{
    let jobText =
    {
        writeInput: INP_MESSAGE_TEXT, value: MSG.text.body, then:
        {
            click: BTN_MESSAGE_SEND, then: () => 
            {
               events.sent()
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
        files: [FILE.storedName], then:
        {
            clickTry: BTN_ATTACH_PHOTO, then: 
            {
                writeInput: INP_ATTACH_TEXT, value: FILE.caption, then:
                {
                    click: BTN_ATTACH_SEND, then: 
                    {
                        waitNotFound: '.Transition_slide-active>.MessageList .last-in-list .ProgressSpinner', then: () =>
                        {
                            events.sent();
                        }
                    }    
                }
            }
        }
    }

    //setTimeout(() => { 
        JOBS.run(jobFile); 
    //}, 250);
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
