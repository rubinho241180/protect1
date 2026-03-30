const ST_DISCONNECTED = 'stDisconnected' ,
      ST_SYNCING      = 'stSyncing'      ,
      ST_CONNECTED    = 'stConnected'    ,

      ST_CONFLICT     = 'stConflict'     ,
      ST_RECSYNCING   = 'stResyncing'    ,
      ST_UNSYNCING    = 'stUnsyncing'    ,
      
      ST_ABORTED      = 'stAborted'      ,
      ST_BLOCKED      = 'stBlocked'      ,
      ST_UNKNOWN      = 'stUnknown'      ;

WAPP.triggerStatus = function()
{
    //STATUS
    var x_mode = WAPP.__status().__x_mode;
    var simpli = '';

    if ((WAPP.status.mode == 'MAIN'    )                && (x_mode == 'QR'      )) simpli = ST_BLOCKED      ; else
    if ((WAPP.status.mode == 'CONFLICT')                && (x_mode == 'QR'      )) simpli = ST_ABORTED      ; else
    if ((WAPP.status.mode.substr(0, 2) == 'QR'      )   && (x_mode == 'SYNCING' )) simpli = ST_SYNCING      ; else
    if ((WAPP.status.mode == 'MAIN'    )                && (x_mode == 'SYNCING' )) simpli = ST_UNSYNCING    ; else
    if ((WAPP.status.mode == 'CONFLICT')                && (x_mode == 'SYNCING' )) simpli = ST_RECSYNCING   ; else
    if                                                     (x_mode == 'QR'      )  simpli = ST_DISCONNECTED ; else
    if                                                     (x_mode == 'MAIN'    )  simpli = ST_CONNECTED    ; else
    if                                                     (x_mode == 'CONFLICT')  simpli = ST_CONFLICT     ; else
                                                                                   simpli = ST_UNKNOWN      ;


    if (x_mode != WAPP.status.mode)
    {
        switch(simpli)
        {
            case ST_SYNCING:
                //WAPP.triggerDevice();
                //WAPP.monitoringProfile();
                //WAPP.monitoringPicture();
                break;
            case ST_CONNECTED:
                WAPP.triggerDevice();
                WAPP.monitoringProfile();
                WAPP.monitoringPicture();
                WAPP.monitoringMessages();
                break;
            case ST_CONFLICT:
                WAPP.stopMonitoringProfile();
                WAPP.stopMonitoringPicture();
                //WAPP.stopMonitoringMessages();
                break;
            case ST_RECSYNCING:
                //WAPP.monitoringProfile();
                //WAPP.monitoringPicture();
                break;
            case ST_UNSYNCING:
                WAPP.stopMonitoringProfile();
                WAPP.stopMonitoringPicture();
                //WAPP.stopMonitoringMessages();
                break;
        }

        console.log(JSON.stringify(
            {
                href    : '/status',
                status  : simpli,
                mode: {ant: WAPP.status.mode, cur: x_mode},
            }
        ));

        WAPP.status.mode       = x_mode;
        WAPP.status.simplified = simpli;
    }

    //STREAM
    var x_info = WAPP.__status().__x_displayInfo;
    var x_simp = '';

    if ((x_info != WAPP.stream.info) && (WAPP.stream.observables.includes(x_info)))
    {
        if (x_info == 'NORMAL' ) x_simp = 'stNormal' ; else
        if (x_info == 'OFFLINE') x_simp = 'stOffline'; else
        if (x_info == 'TIMEOUT') x_simp = 'stTimeout'; 

        console.log(JSON.stringify(
            {
                href  : '/stream',
                stream: x_simp,
                info  : {/*inf: WAPP.__status().__x_info,*/ old: WAPP.stream.info, cur: x_info},
            }
        ));

        WAPP.stream.info = x_info;
    }
}

WAPP.monitoringStatus = function() 
{

    /*try
    {*/
        WAPP.triggerStatus();
        WAPP.__status().off('change').on('change', () => WAPP.triggerStatus());
    /*}
    catch(err)
    {
        console.log('error1: '+err.message);

        console.log(JSON.stringify(
            {
                href    : '/status',
                status  : ST_UNKNOWN,
                mode: {ant: 'WAPP.status.mode', cur: 'QR'},
            }
        ));

        console.log(JSON.stringify(
            {
                href  : '/stream',
                stream: 'stOffline',
                info  : {old: 'XYZ', cur: 'OFFLINE'},
            }
        ));
    }   */ 
}

console.log('---=> 80_status.js');
