WAPP.triggerProfile = function()
{
    //try
    //{
        var newName = WAPP.__meProfile().__x_pushname;

        if (newName)
        {
            if (newName != this.profile.name)
            {
                this.profile.name = newName;

                console.log(JSON.stringify(
                    {
                        href: '/profile',
                        //id: Store.Conn.__x_me._serialized,
                        id: WAPP.__meId(),// Store.Me.__x_wid._serialized, //petrolçina
                        name: newName,
                        //formattedId: Store.Contact.get(Store.Me.__x_wid).__x_formattedUser.substring(1,50),
                        formattedId: WAPP.__meNumber(), //Store.Me.__x_wid.user, //petrolina
                    }
                ));
            }
        }
    //}
    //catch(err)
    //{
    //    console.log('the profile is not ready yet');
   // }
}

WAPP.monitoringProfile = function() 
{
    if (!WAPP.profile.monitoring)
    {
        try
        {
            WAPP.triggerProfile();
            WAPP.__meProfile().off('change').on('change', () => WAPP.triggerProfile());
            WAPP.profile.monitoring = true;
            console.log('monitoringProfile: ON;');

            //clearInterval(ok);
        }
        catch(err)
        {
            console.log('errorr: '+err.message);
        }
    }

}

WAPP.stopMonitoringProfile = function() 
{
    WAPP.__meProfile().off('change');
    WAPP.profile.monitoring = false;
    console.log('monitoringProfile: OFF;');
}

console.log('---=> 30_profile.js');
