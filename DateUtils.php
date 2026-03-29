<?php 

namespace DateUtils;

class Date
{

	public $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

  	public static function date($input = NULL) {
  		return ($input) ? date("d-m-Y", strtotime($input)) : date("d-m-Y");
  	}

  	public static function time($time = NULL) {
  		return date("H:i", strtotime($time));
  	}

  	public static function timestamp($time = NULL) {
  		return date('Y-m-d H:i:s', strtotime($time));
  	}


  	public static function diff($dfrom, $dto) {
		$start = strtotime($dfrom);
		$end   = strtotime($dto);

		$ret = new \stdClass();

		if ($dto == NULL) {
			$ret->days = NULL;
		} else

		if ($end > $start) {
			$ret->days = intval(ceil(abs($end - $start) / 86400));
		} else {
			$ret->days = intval(ceil(abs($end - $start) / 86400)) *-1;
		}


		return $ret;  		
  	}
}



class Chronometer
{
	public $start;

	public function __construct() {
		$this->start = microtime(true);
	}

	public function seconds() {
		return round(microtime(true) - $this->start, 3);
	}
}