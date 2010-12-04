<?php

/**
 * Model for events
 */
include_once(APPPATH.'models/Base.php');

class Event extends Base 
{
	public $columns = array(
		'event_name'	=> array('TYPE'=>'varchar(200)'),
		'event_start'	=> array('TYPE'=>'int(11)'),
		'event_end'		=> array('TYPE'=>'int(11)'),
		'event_lat'		=> array('TYPE'=>'decimal(20,16)'),
		'event_long'	=> array('TYPE'=>'decimal(20,16)'),
		'id'			=> array('TYPE'=>'int(11)','PRIMARY_KEY'=>true,'VALUE'=>null),
		'event_loc'		=> array('TYPE'=>'mediumtext'),
		'event_desc'	=> array('TYPE'=>'mediumtext'),
		'active'		=> array('TYPE'=>'int(11)'),
		'event_stub'	=> array('TYPE'=>'varchar(30)'),
		'event_icon'	=> array('TYPE'=>'varchar(30)')
		/* more columns go here */
	);
	
	public $values = array(
		
	);
	
	public function __construct($id = null)
	{
		if($id){
			// load the event...
			$this->getById($id);
		}
	}
	
}