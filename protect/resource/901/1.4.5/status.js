STATUS = {

    status: "unknown",

    points: {

        disconnected: {
            has: [],
            not: [],
            status: "stDisconnected",
            key: "disconnected"
        },
        syncing: {
            has: [],
            not: [],
            status: "stSyncing",
            key: "syncing"
        },
        connected: {
            has: [],
            not: [],
            status: "stConnected",
            key: "connected"
        },
        conflict: {
            has: [],
            not: [],
            status: "stConflict",
            key: "conflict"
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
            STATUS.points.connected
        ],

        syncing: [
            STATUS.points.connected,
            STATUS.points.disconnected
        ],

        connected: [
            STATUS.points.conflict,
            STATUS.points.disconnected
        ],
        
        conflict: [
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
    STATUS.points.disconnected.has = points.disconnected.has;
    STATUS.points.syncing.has      = points.syncing.has;
    STATUS.points.connected.has    = points.connected.has;
    STATUS.points.conflict.has     = points.conflict.has;

    STATUS.points.disconnected.not = !!points.disconnected.not ? points.disconnected.not : [];
    STATUS.points.syncing.not      = !!points.syncing.not      ? points.syncing.not      : [];
    STATUS.points.connected.not    = !!points.connected.not    ? points.connected.not    : [];
    STATUS.points.conflict.not     = !!points.conflict.not     ? points.conflict.not     : [];

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


console.log('STATUS INJECTED!');