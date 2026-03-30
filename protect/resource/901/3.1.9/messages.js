WAPP = {
    profile: {
        id: "",
    },
};


opennedChatId = '';

BTN_ATTACH = '.stacked-compose-buttons [data-e2e-picker-button="ATTACHMENT"]'; // '.input-row [data-e2e-picker-button="ATTACHMENT"]'

function openChat(phone, callback)
{
    console.log('openChat()');

    if (opennedChatId == phone)
    {
        callback();
        return false;
    }

    JOBS.run({

        click: '.start-chat a', then: 
        {
            //write phone
            writeInput: '.input', value: phone, then: 
            {   
                //select contact button
                click: 'mw-contact-selector-button button', then: 
                {
                    waitFound: 'textarea', then: () =>
                    {
                        opennedChatId = phone;
                        callback();
                    }
                }
            }
        }
    });
}


function isVisible(el) {
  const rect = el.getBoundingClientRect();

  return (
    rect.top < window.innerHeight &&
    rect.bottom > 0 &&
    rect.left < window.innerWidth &&
    rect.right > 0
  );
}


function sendMsg(messages, idx, events)
{
    BtnInline = document.querySelector('.send-button.inline');
    console.log('111111111111111:', BtnInline);
    BtnInlineIsVisible = isVisible(BtnInline);
    BtnTarget = BtnInlineIsVisible ? '.send-button.inline' : '.send-button:not(.inline)';

    console.log('******* Will send with '+BtnTarget);

// function sendMsg(messages, idx, events)
// {
    var msg = messages[idx];

    if (msg.files.length > 0)
    {
        console.log('oiee: file ' + decodeURIComponent(msg.files[0].filePath) );
        var job =
        {
            //files
            files: [ decodeURIComponent(msg.files[0].filePath) ], then:
            {
                //attach
                click: BTN_ATTACH, then:
                {
                    //wait thumbnail
                    waitFound: '.thumbnail', then:
                    {
                        //write caption
                        writeInput: 'textarea', value: decodeURIComponent(msg.files[0].caption), then:
                        {
                            //sendclick
                            click: BtnTarget, then:
                            {
                                //wait thumbnails hide
                                waitNotFound: '.thumbnail', then: () =>
                                {
                                    //waitNotFound: 'mws-message-wrapper:last-of-type mws-icon.sending-icon', then: () =>
                                    //{
                                        SetEnabled(true);

                                        //callback
                                        //if (mySelectorAll('mws-message-wrapper:last-of-type .failed,mws-message-wrapper:last-of-type .red-highlighted').length == 0)
                                            events.sent();// else
                                        //    events.fail();
                                    //}
                                }
                            }
                        }
                    }
                }
            }
        }
    } else
    //if (!!msg.text)
    {
        console.log('oiee: tetx ' + decodeURIComponent(msg.text.body) );
        var job =
        {
            //write msg
            writeInput: 'textarea', value: decodeURIComponent(msg.text.body), then:
            {
                //send msg
                //click: '.send-button:last', then: () =>
                click: BtnTarget, then: () =>
                {
                    //waitNotFound: 'mws-message-wrapper:last-of-type mws-icon.sending-icon', then: () =>
                    //{
                        SetEnabled(true);

                        //callback
                        //if (mySelectorAll('mws-message-wrapper:last-of-type .failed,mws-message-wrapper:last-of-type .red-highlighted').length == 0)
                            events.sent();// else
                            //events.fail();
                    //}
                }
            }
        }

    } 



    JOBS.run(job);
    
}




WAPP.sendBatch = function(SND, idx = 0)
{

    SetEnabled(false, SND.debug);

    openChat(SND.batch.target.address, function() {
        console.log('sendBatch.openChat');
        console.log(JSON.stringify(
                    SND
                ));

        sendMsg(SND.batch.messages, idx, {

            fail: function() 
            {
                SND.status = 500;
                console.log(JSON.stringify(
                    SND
                ));  
                SetEnabled(true);
            },
            
            sent: function() 
            {
                console.log('idx: ' + idx);
                console.log('len: ' + (SND.batch.messages.length-1));
                if (idx < (SND.batch.messages.length-1)) {
                    setTimeout(function () {
                        WAPP.sendBatch(SND, idx + 1);
                    }, 5000);
                } else {
                    console.log('ENDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD');
                    SND.status = 200;
                    console.log(JSON.stringify(
                        SND
                    ));  
                    
                    SetEnabled(true);    
                }

                    

            }
        });

    });
}






console.log('messages.js INJECTED!');
