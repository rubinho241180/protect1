<?php 

error_reporting( E_ALL );


require_once __DIR__ . '/activerecord/ActiveRecord.php';

// initialize ActiveRecord
ActiveRecord\Config::initialize(function($cfg)
{
    $cfg->set_model_directory(__DIR__ . '/models');
    $cfg->set_connections(array('development' => 'mysql://r2_read:Xyz@master321321@localhost/r2'));

	// you can change the default connection with the below
    //$cfg->set_default_connection('production');
});
