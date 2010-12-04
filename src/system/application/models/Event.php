<?php

/**
 * Model for events
 */
include_once(APPPATH.'models/Base.php');

class Event extends Base 
{
	public $columns = array(
		'event_name'	=> 'varchar(200)',
		'event_start'	=> 'int(11)',
		'event_end'		=> 'int(11)',
		'event_lat'		=> 'decimal(20,16)',
		'event_long'	=> 'decimal(20,16)',
		'ID'			=> array('TYPE'=>'int(11)','PRIMARY_KEY'=>true),
		'event_loc'		=> 'mediumtext',
		'event_desc'	=> 'mediumtext',
		'active'		=> 'int(11)',
		'event_stub'	=> 'varchar(30)',
		'event_icon'	=> 'varchar(30)'
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