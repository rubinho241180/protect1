<?php 

class MY_Controller extends CI_Controller {
	public function __construct() {
	     parent::__construct();
	     //if (! is_admin())
	     //{
	     //     show_404();
	     //}
		date_default_timezone_set("America/Recife");

	} 
}