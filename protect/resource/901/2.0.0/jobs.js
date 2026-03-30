JOBS = {
    sel: null,
    eventName: '',
    elementName: '',
    setEventName: function(name, el)
    {
        this.eventName   = name || 'ops!!!' ;
        this.elementName = el;

        //console.log('will: '+this.eventName +' on '+ this.elementName);
    }
};

JOBS.runOnEnd = function(job)
{
   // console.log('executed: '+ JOBS.eventName + ' on '+ JOBS.elementName);
    if (typeof job.then == 'function')
    {
        setTimeout(function() {
            job.then();
        }, job.delay || 0);

    } else
    
    {
        setTimeout(function() {
            JOBS.run(job.then);    
        }, job.delay || 0);
            
    } 
        
}

JOBS.run = function(job)
{
    JOBS.sel = job.click || job.clickTry || job.waitFound || job.waitNotFound || job.writeInput || JOBS.sel;


    //added petrolina 01-04-21
    if ((typeof JOBS.sel == 'string') && (JOBS.sel.substr(0,2) == '//')) {
        var JOBS_length = $(getElementByXPath(JOBS.sel)).length;
    } else {
        var JOBS_length = $(JOBS.sel).length;
    }


    var eventName = 'sem nome';

   //console.log('will: '+JOBS.sel);



    if ('clickTry' in job)
    {
        if (JOBS_length == 0)
        {
            console.log('JOBS.clickTry ERROR: "'+JOBS.sel+'" not found.');
        }
        //JOBS.setEventName('click', job.click);
        simulateClick(JOBS.sel, () => {

            JOBS.runOnEnd(job)
        })
    }
    
    if ('click' in job)
    {
        if (JOBS_length == 0)
        {
            console.log('JOBS.click ERROR: "'+JOBS.sel+'" not found.');
        }
        //JOBS.setEventName('click', job.click);
        sendRealClick(JOBS.sel, () => {

            JOBS.runOnEnd(job)
        })
    }
    
    if ('waitFound' in job)
    {
        //JOBS.setEventName('waitFound', job.waitFound);
        waitFound(JOBS.sel, () => {

            JOBS.runOnEnd(job)
        })
    }
    
    if ('waitNotFound' in job)
    {
        //JOBS.setEventName('waitNotFound', job.waitNotFound);
        waitNotFound(JOBS.sel, () => {

            JOBS.runOnEnd(job)
        })
    }

    if ('writeInput' in job)
    {
        //JOBS.setEventName('writeInput', job.writeInput);

        writeInput(JOBS.sel, job.value, () => {
            JOBS.runOnEnd(job)
        })
    }

    if ('files' in job)
    {
        //JOBS.setEventName('writeInput', job.writeInput);


        console.log(JSON.stringify({
            href: "/files",
            files: [
                {
                    name: job.files
                }
            ]
        }));

        JOBS.runOnEnd(job)
    }

   // console.log('foi: '+eventName+' on '+JOBS.sel);


    if ('focusOut' in job)
    {
        //JOBS.setEventName('writeInput', job.writeInput);

        //var event = new Event('focus');
        //$(JOBS.sel)[0].dispatchEvent(event);
        
        var event = new Event('blur');
        $(JOBS.sel)[0].dispatchEvent(event);
        
        //$(JOBS.sel).trigger("focusout");
        JOBS.runOnEnd(job);
    }

    
}

//*************************************************************************

var jobs = 
{
    click: '.attach', then:
    {
        waitFound: '.thumbnail', then:
        {
            click: '.send', then:
            {
                waitNotFound: '.thumbnail', then: () => 
                {
                    alert(':)');
                }
            }
        }
    }
};


//JOBS.run(jobs);

console.log('jobs.js is INJECTED!');