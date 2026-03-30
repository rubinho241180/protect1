function openSearch(callback, callbackDelay) 
{
    //.click();
    simulateClick('[ng-click="openContacts()"]');

    $('.contacts_modal_wrap').css('display', 'none');

    waitFound('.contacts_modal_search_field', function()
    {
        callback({msg: 'clip callback!'});
    });

}

opennedChatId = '';

function openChat(phone, obj)
{
    if (opennedChatId == phone)
    {
        obj.found();
        return false;
    }


    openSearch(function() {


        //open Import Dlg
        simulateClick('[ng-click="importContact()"]');

        //write phone
        writeInput('[name=phone]', phone);

        //doImport
        simulateClick('[ng-click="doImport()"]');


        //espera o campo phone aparecer na tela
        waitNotFound('[name=phone]', function() {

            //se NÃO deu msg de erro, então existe
            if ($('[ng-click="$dismiss()"]').length === 0) {
                
                opennedChatId = phone;
                obj.found();

            } else 
            //se deu msg de erro, então existe
            {

                simulateClick('[ng-click="$dismiss()"]');

                setTimeout(function() {
                    simulateClick('[ng-click="$dismiss()"]');
                }, 1);
                
                obj.notFound();
            }


            //obj.finish();
        })

    });


}


function writeMsg(sBody, callback, callbackDelay) {
    

    waitFound(".composer_rich_textarea", function(el) {


        var input = $(".composer_rich_textarea");
        $(input).text( sBody );

        //exibe button
        $('button[type=submit]').css('position', 'static');
        $('form').removeClass('im_send_form_empty');


        //callback
        if (typeof callback === "function") {
            setTimeout(function() {
                callback({msg: 'clip callback!'});
            }, callbackDelay || 1);
        }   
    });

}


function waitSend(events) {


        //aguarda a mensagem ser enviada ou não
        var last_msg = '.im_history_messages_peer:not(.ng-hide) .im_history_message_wrap:has(.im_message_out):last';


        var int5 =
        setInterval(function() {

            //FAIL
            if ($(last_msg).hasClass('im_message_error')) {
                clearInterval(int5);

                setTimeout(function() {
                    simulateClick('[ng-click="$dismiss()"]');
                }, 100);

                //batch.status = 500;
                //console.log(JSON.stringify(
                //    batch
                //));                        
                events.fail();
            }

            //SENT
            if ($(last_msg+':not(.im_message_pending)').length == 1) {
                clearInterval(int5);

                //batch.status = 200;
                //console.log(JSON.stringify(
                //    batch
                //));                        
                events.sent();
            }

        }, 100);
}


function sendMsg(msg, events) {


    if (!!msg.text)
    {
        writeMsg(Base64.decode(msg.text.body) , function()
        {
            msg.text.body = 'cleaned!!!';

            sendRealClick('button[type=submit]', function()
            {
                waitSend({
                    fail: function()
                    {
                        events.fail();
                    },

                    sent: function()
                    {
                        events.sent();  
                    }
                });
            });
        });
    } 

    else

    if (!!msg.file)
    {

        console.log(JSON.stringify({
            href: "/files",
            files: [
                {
                    name: msg.file.base64
                }
            ]
        }));

        msg.file.base64 = 'b64: cleaned!!!';

        sendRealClick('.icon-camera', function()
        {
            waitSend({
                fail: function()
                {
                    events.fail();
                },

                sent: function()
                {
                    events.sent();  
                }
            });
        });
    }

}




WAPP = {
    profile: {
        id: "",
    },
    /*
    status: {
        cur: "stUnknown",
        old: undefined,
    },
    */
};




WAPP.sendBatch = function(batch, idx = 0)
{
    //delete batch.messages.listHelper;

    batch = Object.assign(
        {
            href: '/batch',
        },
        batch
    );


    openChat(batch.target.id, {

        //phone: batch.target.id,
        found: function()
        {


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
                        idx++;
                        WAPP.sendBatch(batch, idx)
                    } else

                    {
                        batch.status = 200;
                        console.log(JSON.stringify(
                            batch
                        ));  
                    }

                }
            });
        },
        
        notFound: function()
        {
            batch.status = 404;
            console.log(JSON.stringify(
                batch
            ));                        

        }
    })
}


/*
window.imageToDataUri = function (uri, width, height, done)
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

                    ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, width, height);

                    done(canvas.toDataURL());
                }
}
*/




SEL_CONNECTED    = '.icon-hamburger-wrap';
SEL_SYNCING      = '.tg_head_connecting_wrap';
SEL_DISCONNECTED = '.login_phone_code_input_group:first';
SEL_CONFLICT     = 'mat-dialog-container:first';



STATUS.start(
    {
        syncing: {
            has: [
                SEL_SYNCING,
            ]
        },
        conflict: {
            has: [
                SEL_CONFLICT,
            ]
        },
        connected: {
            has: [
                SEL_CONNECTED
            ],
            not: [
                SEL_SYNCING,
                SEL_CONFLICT,
            ]
        },
        disconnected: {
            has: [
                SEL_DISCONNECTED,
            ]
        }
    },
    {
        connected: {
            override: function()
            {
                setTimeout(function() {

                    //open profile and extract number
                    simulateClick('[ng-click="openSettings()"]');

                    $('.modal-backdrop').css('opacity', '0.1');


                    WAPP.profile.id = $('[ng-bind^="profile.phone"]').text();

                    /*
                    setTimeout(function() {
                    */

                        //profile
                        console.log(JSON.stringify(
                            {
                                href: '/profile',
                                id: WAPP.profile.id.replace(/[^0-9]/g, ''),
                                name: WAPP.profile.id,
                                formattedId: WAPP.profile.id,
                            }
                        ));


                        /*
                        //picture
                        if ($('img.peer_modal_photo').length > 0)
                        {
                            var url = $('img.peer_modal_photo').attr('src');

                            window.imageToDataUri(url, 28, 28, function(uri)
                                {
                                    console.log(JSON.stringify(
                                        {
                                            href: '/picture',
                                            isMe: true,
                                            id: 'xyz',
                                            uri: uri.split(',')[1],
                                        }
                                    ));
                                }
                            );
                        } else {
                            console.log('ELSE...');
                        }
                        */


                        simulateClick('[ng-click="$close()"]');


                        //status
                        STATUS.trigger(
                            'stConnected'
                        );

                    /*
                    }, 10000);
                    */


                }, 1000);
            }
        }
    }
);


//CSS
$('.tg_head_split, .im_page_wrap').css('max-width', '100%');


//port
console.log(JSON.stringify(
    {
        href: '/port',
        status: 200,
        description: 'OK',
    }
));

//device
console.log(JSON.stringify(
    {
        href: '/device',
        manufacturer: 'man',
        model: 'UNK',
    }
));


console.log('INJECTED!');