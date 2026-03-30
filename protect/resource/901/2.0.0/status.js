STATUS = {

    status: "unknown",

    points: {

        disconnected: {
            has: [],
            not: [],
            status: "stUnLogged",
            key: "disconnected"
        },
        syncing: {
            has: [],
            not: [],
            status: "stLoggining",
            key: "syncing"
        },
        connected: {
            has: [],
            not: [],
            status: "stLogged",
            key: "connected"
        },
        conflict: {
            has: [],
            not: [],
            status: "stConflict",
            key: "conflict"
        },
        internet: {
            has: [],
            not: [],
            status: "stNoInternet",
            key: "internet"
        }
    },

};

STATUS.watches = {

        unknown: [
            STATUS.points.disconnected,
            STATUS.points.syncing,
            STATUS.points.connected
        ],

        disconnected: [
            STATUS.points.syncing,
            STATUS.points.connected,
            STATUS.points.internet
        ],

        syncing: [
            STATUS.points.connected,
            STATUS.points.disconnected
        ],

        connected: [
            STATUS.points.conflict,
            STATUS.points.disconnected,
            STATUS.points.internet
        ],
        
        conflict: [
            STATUS.points.connected,
            STATUS.points.disconnected
        ],
        
        internet: [
            STATUS.points.connected,
            STATUS.points.disconnected
        ],
        
    };


STATUS.trigger = function(astatus)
{
    console.log(JSON.stringify(
        {
            href: "/status",
            status: astatus,
        }
    ));
}

STATUS.start = function(points, events = {})
{
    /*
    try {
    STATUS.points.disconnected.has = points.disconnected.has;
    } catch (e) {

    }
    */

    STATUS.points.disconnected.has = !!points.disconnected ? points.disconnected.has : STATUS.points.disconnected.has;
    STATUS.points.syncing.has      = !!points.syncing      ? points.syncing.has      : STATUS.points.syncing.has     ;
    STATUS.points.connected.has    = !!points.connected    ? points.connected.has    : STATUS.points.connected.has   ;
    STATUS.points.conflict.has     = !!points.conflict     ? points.conflict.has     : STATUS.points.conflict.has    ;
    STATUS.points.internet.has     = !!points.internet     ? points.internet.has     : STATUS.points.internet.has    ;

    STATUS.points.disconnected.not = !!points.disconnected ? points.disconnected.not || [] : [];
    STATUS.points.syncing.not      = !!points.syncing      ? points.syncing.not      || [] : [];
    STATUS.points.connected.not    = !!points.connected    ? points.connected.not    || [] : [];
    STATUS.points.conflict.not     = !!points.conflict     ? points.conflict.not     || [] : [];
    STATUS.points.internet.not     = !!points.internet     ? points.internet.not     || [] : [];

    /*
    STATUS.points.disconnected.has = points.disconnected.has;
    STATUS.points.syncing.has      = points.syncing.has;
    STATUS.points.connected.has    = points.connected.has;
    STATUS.points.conflict.has     = points.conflict.has;
    STATUS.points.internet.has     = points.internet.has;
    */

    /*
    STATUS.points.disconnected.not = !!points.disconnected.not ? points.disconnected.not : [];
    STATUS.points.syncing.not      = !!points.syncing.not      ? points.syncing.not      : [];
    STATUS.points.connected.not    = !!points.connected.not    ? points.connected.not    : [];
    STATUS.points.conflict.not     = !!points.conflict.not     ? points.conflict.not     : [];
    STATUS.points.internet.not     = !!points.internet.not     ? points.internet.not     : [];
    */

    //console.log('...');

    setInterval(function() {
        //console.log('x: '+STATUS.status);

        STATUS.watches[STATUS.status].forEach(function(e, i)
        {
            fhas = e.has.join();
            fnot = e.not.join();

            //console.log(STATUS.status, e);

            /*
            */

            if (($(fhas).length == fhas.split(',').length) && ($(fnot).length == 0))
            //if ((document.querySelectorAll(fhas).length == fhas.split(',').length) && (document.querySelectorAll(fnot).length == 0))
            {
                STATUS.status = e.key;
    
                //console.log(STATUS.status+', then will watching', STATUS.watches[STATUS.status]);
    

                if ((!!events[STATUS.status]) && (events[STATUS.status].before))
                {
                    events[STATUS.status].before();
                }

                /*
                console.log('has: '+ fhas);
                console.log('has.length: '+ $(fhas).length);
                console.log('has.split.length: '+fhas.split(',').length);
                console.log('not: ' + fnot);
                console.log('not.length: ' + $(fnot).length);
                */
                //console.log('override: '+ STATUS.status);
                if ((!!events[STATUS.status]) && (!!events[STATUS.status].override))
                {
                    events[STATUS.status].override();

                } else {

                    STATUS.trigger(
                        STATUS.points[STATUS.status].status
                    );
                }


                if ((!!events[STATUS.status]) && (events[STATUS.status].after))
                {
                    events[STATUS.status].after();
                }

                return;
            } //else console.log('not');

        });
    }, 100);
}


console.log('STATUS INJECTED! 2.0.0');