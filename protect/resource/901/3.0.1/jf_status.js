STATUS = {

    status: "unknown",

    points: {
        unknown: {
            watch: [
                "disconnected", 
                "syncing",
                "connected"
            ]
        },

        disconnected: {
            status: "stUnlogged",
            key: "disconnected",
            watch: [
                "syncing", 
                "connected"
            ]
        },
        syncing: {
            status: "stSyncing",
            key: "syncing",
            watch: [
                "disconnected", 
                "connected"
            ],
        },
        connected: {
            status: "stLogged",
            key: "connected",
            watch: [
                "disconnected",
                "blocked"
            ]
        },
        blocked: {
            status: "stBlocked",
            key: "blocked",
            watch: []
        },
        // conflict: {
        //     has: [],
        //     not: [],
        //     status: "stConflict",
        //     key: "conflict"
        // }
    },



    injected: false,
};


STATUS.check = function(points)
{
    STATUS.points[STATUS.status].watch.forEach(function(watch, i)
    {
        if (!points[watch]) 
            return;
        
        e = {
            key: STATUS.points[watch].key,
            has: points[watch].has || [],
            not: points[watch].not || []
        }

        fhas = e.has.join();
        fnot = e.not.join();

        if ((mySelectorAll(fhas).length == e.has.length) && (mySelectorAll(fnot).length == 0))
        {
            STATUS.status = e.key;

            //PRINT INJECT
            if (!!points[STATUS.status] && !!points[STATUS.status].inject && !STATUS.injected)
            {
                STATUS.injected = true;

                console.log(JSON.stringify(
                    {
                        href: '/inject'
                    }
                ));

                //while (!STATUS.injected) {
                //    console.log('waiting [STATUS.injected]');
               // }
            }

            //BEFORE
            if ((!!points[STATUS.status]) && (!!points[STATUS.status].before))
            {
                points[STATUS.status].before();
            }

            if ((!!points[STATUS.status]) && (!!points[STATUS.status].override))
            {
                points[STATUS.status].override();

            } else {

                //PRINT STATUS
                console.log(JSON.stringify(
                    {
                        href: "/status",
                        status: STATUS.points[STATUS.status].status,
                    }
                ));
            }


            //AFTER
            if ((!!points[STATUS.status]) && (!!points[STATUS.status].after))
            {
                points[STATUS.status].after();
            }

            return;
        } 
    });
}


STATUS.start = function(points)
{
    fpoints = points;
    STATUS.check(points);

    //DOM CHANGE
    MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

    var observer = new MutationObserver(function(mutations, observer) {
        // fired when a mutation occurs
        //console.log(mutations, observer);

        if (!!window.finterval)
            clearInterval(finterval);

        finterval = setInterval(function() {
            clearInterval(finterval);
            STATUS.check(fpoints);
        }, 100);

        // ...
    });

    // define what element should be observed by the observer
    // and what types of mutations trigger the callback
    observer.observe(document, {
      subtree: true,
      attributes: true
      //...
    });    
}


console.log('STATUS INJECTED!');