<?php
class Ins extends ActiveRecord\Model
{
	static $table_name = "ins";
	static $belongs_to = array( array('cus') /*,array('author')*/);	
	// a person can have many orders and payments
	/*static $has_many = array(
		array('orders'),
		array('payments'));

	// must have a name and a state
	static $validates_presence_of = array(
		array('name'), array('state'));*/
}
?>
