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

        click: '.start-chat a:first', then: 
        {
            //wait search input
            waitFound: '.input', then: 
            {
                //write phone
                writeInput: null, value: phone, then: 
                {
                    //wait contact button
                    waitFound: 'mw-contact-selector-button button:first', then: 
                    {
                        //select contact button
                        click: null, then: 
                        {
                            waitFound: 'textarea', then: () =>
                            {
                                opennedChatId = phone;
                                callback();
                            }
                        }
                    }
                }
            }   
        }
    });
}


function sendMsg(msg, events)
{
     
    console.log('oiee');
    if (!!msg.text)
    {
        console.log('oiee: tetx');
        var job =
        {
            //write msg
            writeInput: 'textarea', value: Base64.decode( msg.text.body ), then:
            {
                //send msg
                click: '.send-button:last', then: () =>
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
                click: '[data-e2e-picker-button="ATTACHMENT"]:last', then:
                {
                    //wait thumbnail
                    waitFound: '.thumbnail', then:
                    {
                        //write caption
                        writeInput: 'textarea', value: Base64.decode( msg.file.caption ), then:
                        {
                            //sendclick
                            click: '.send-button:last', then:
                            {
                                //wait thumbnails hide
                                waitNotFound: '.thumbnail', then: () =>
                                {
                                    events.sent();
                                }
                            }
                        }
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

        sendMsg(batch.messages.listHelper[idx], {

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
                    console.log(idx +' of '+(batch.messages.listHelper.length-1));
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


WAPP.login = function(credentials)
{
    console.log('will login: '+credentials);

    credentials = iniToObj(Base64.decode(credentials));

    var job =
    {
        //write msg
        writeInput: '//*[@id="identifierId"]', value: credentials.Email, then:
        {
            //send msg
            click: '#identifierNext', then: () =>
            {
                //alert('clicked');
                //callback
                events.sent();
            }
        }
    }

    JOBS.run(job);
}



console.log('messages.js INJECTED!');
