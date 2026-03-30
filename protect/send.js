
$(document).ready(function(){


    function addChat(input) {


      var hli = $('<li>'  , {class: 'hli', id: 'chat-'+input.id});
      var inn = $('<div>' , {class: 'inn'});
      var hdr = $('<div>' , {class: 'hdr', text: input.text});
      var vul = $('<ul>'  , {class: 'vul'});
      //var vli = $('<li>'  , {class: 'vli'});
      var txt = $('<form>', {class: 'txt'});
      var inp = $('<div>' , {class: 'inp', contenteditable: true, placeholder: "Message...", onkeypress: "return (this.innerText.length <= 160)"});
      var btn = $('<button>'  , {class: 'btn'});


      inp.text('Here is another solution using css transform (for performance purposes on mobiles, see answer of @mate64 ) without having to use animations and keyframes.');

      hdr.appendTo(inn);

      //vli.appendTo(vul);
      vul.appendTo(inn);

      inp.appendTo(txt);
      btn.appendTo(txt);
      txt.appendTo(inn);

      inn.appendTo(hli);

      hli.prependTo('.reminders').addClass('show');

      inp.focus();

      if ($('.hli').length > 10) {
          $('.hli').last().addClass('hide');
          setTimeout(function() {
            $('.hli').last().remove();
          }, 400);
      }

    }




    //$('#add-chat').on('click', function(){
    //    addChat();

    //});  



    $(document).on('submit', '.txt', function(ev) {
        ev.preventDefault(); // to stop the form from submitting
        /* Validations go here */
        //this.submit(); // If all the validations succeeded
        var txt = $(this).find('.inp').text().trim(); 
        if (txt != '') {
            sndAjx({id: 1, to: '5581998636361', text: txt}, $(this).parents('.hli'));
        }
        //addChat();
    });


    /*$(document).on('keydown', '.inp', function(event) {
    // enter has keyCode = 13, change it if you want to use another button
      if (event.keyCode == 13) {
         // $(this).parent().submit();
         // return false;
        //ev.stopPropagation();
        //ev.preventDefault(); // to stop the form from submitting
      }
    });*/

    $(document).on('keypress', '.inp', function(e){
      if (e.which != 13) {
        return true;
      } else {
          e.preventDefault();
          e.stopPropagation();
          $(this).parent().submit();
          return false;
        }
    });  


    $(document).on('paste', '.inp', function(e){
        // cancel paste
        e.preventDefault();

        // get text representation of clipboard
        var text = (e.clipboardData || window.clipboardData || e.originalEvent.clipboardData).getData("text/plain");

        // insert text manually
        document.execCommand("insertHTML", false, text);
    });  




   // addChat();




    function sndAjx(amsg, ahli) {

        var vli = $('<li>' , {class: 'vli'});
        var msg = $('<div>', {class: 'msg sending', text: amsg.text});

        msg.appendTo(vli);
        vli.appendTo(ahli.find('.vul'));
        ahli.find('.txt .inp').text('').focus();


        $.ajax({
            url: 'http://r2.rfidle.com/protect/sms.php',
            type: 'GET',
            data: {
                id: amsg.id,
                to: amsg.to,
                text: amsg.text,
            },
            //contentType: "application/json; charset=utf-8",
            dataType: "json",
            cache: false,
            success: function(data, textStats, XMLHttpRequest){ 
                msg.removeClass('sending').addClass('sent');
                $("#credit").text(data.cred);
                //alert('success!');
                //console.log(data);
                //$('<pre><code>'+JSON.stringify(data, undefined, 2)+'</code></pre>').insertAfter('#h5'+data.id);
                //vli.removeClass('sending').addClass('sent');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error - xhr.status: '+xhr.status);
                alert('error - thrownError: '+thrownError);
            }
        });
    }











    /*$(document).on('click', '#add-chat', function(){
        var phone = $(this).parent().find('#phone').text().trim(); 
        if (phone != '') {
            addChat();
        }
    });  */



    $(document).on('submit', '#form-phone', function(ev) {
        ev.preventDefault(); 
        var ephon = $(this).find('#phone');
        var sphon = ephon.val().trim(); 
        if (sphon != '') {
            addChat(sphon);
            ephon.val('');
        }
        //addChat();
    });


    $(document).on('keypress', '#phone', function(e){
      if (e.which != 13) {
        return true;
      } else {
          //$('#add-chat').click();
          e.preventDefault();
          e.stopPropagation();
          $(this).parent().submit();
          return false;
        }
    });  






});