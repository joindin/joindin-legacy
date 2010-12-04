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

    public $orm = array(
        'eventComments' => array(
            'table' => 'event_comments',
            'key'   => 'ID = event_comments.event_id'
        )
    );
	
	public $values = array( );

    public $table = 'events';
	
	public function __construct($id = null)
	{
		if($id){
			// load the event...
			$this->getById($id);
		}
	}
	
}