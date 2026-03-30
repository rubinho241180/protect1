WAPP = {
    profile: {
        id: "",
    },
};


opennedChatId = '';

function openChat(phone, callback)
{
    if (opennedChatId == phone)
    {
        callback();
        return false;
    }

    JOBS.run({

        click: '#newText', then: 
        {
            //wait search input
            waitFound: '.newConversationTextField', then: 
            {
                //write phone
                writeInput: null, value: phone, then: 
                {
                    //set
                    focusOut: null, then: 
                    {
                        waitFound: '.uikit-badge:first', then: () =>
                        {
                            opennedChatId = phone;
                            callback();
                        }
                    }
                }
            }   
        }
    });
}


function sendMsg(msg, events)
{
    
    if ($('.uikit-badge--danger').length == 1)
    {
        events.fail();
        return;
    }

    console.log('oiee');
    if (!!msg.text)
    {
        console.log('oiee: tetx');
        var job =
        {
            //write msg
            writeInput: '#text-input', value: Base64.decode( msg.text.body ), then:
            {
                //send msg
                click: '#send_button:last', then: () =>
                {
                    //callback
                    events.sent();
                }
            }
        }

    } else

    if (!!msg.file)
    {
        console.log('oiee: file');
        var job =
        {
            //files
            files: msg.file.base64, then:
            {
                //attach
                click: '.gallery-btn:last', then:
                {
                    //wait thumbnail
                    waitFound: '#send_photo_button', then:
                    {
                        //write caption
                        //writeInput: '#text-input', value: Base64.decode( msg.file.caption ), then:
                        //{
                            //sendclick
                            click: '#send_photo_button', then: 
                            {
                                waitNotFound: null, then: () =>
                                {
                                    events.sent();
                                }
                            }
                        //}
                    }
                }
            }
        }
    }

    JOBS.run(job);
    
}




WAPP.sendBatch = function(batch, idx = 0)
{

    batch = Object.assign(
        {
            href: '/batch',
        },
        batch
    );

    openChat(batch.target.id, function() {
        console.log('sendBatch.openChat');
        console.log(JSON.stringify(
                    batch
                ));

        sendMsg(batch.messages.items[idx], {

            fail: function() 
            {
                batch.status = 500;
                console.log(JSON.stringify(
                    batch
                ));  
            },
            
            sent: function() 
            {

                if (idx < batch.messages.listHelper[0]-1)
                {
                    console.log(idx +' of '+(batch.messages.listHelper[0]-1));
                    idx++;
                    WAPP.sendBatch(batch, idx);
                } else

                {   console.log('ENDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD');
                    batch.status = 200;
                    console.log(JSON.stringify(
                        batch
                    ));  
                }

            }
        });

    });
}






console.log('textnow.js INJECTED!');
