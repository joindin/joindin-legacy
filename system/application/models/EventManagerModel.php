<?php
/**
 * Class EventManagerModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** EventModel */
require_once BASEPATH . 'application/models/EventModel.php';
/** UserModel */
require_once BASEPATH . 'application/models/UserModel.php';

/**
 * Represents the manager for an event.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class EventManagerModel extends DomainModel 
{

    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'event_managers';
    
    /**
     * @see DomainModel::$_hasOne
     */
    protected $_hasOne = array (
        'Event' => array (
            'className' => 'EventModel',
            'referenceColumn' => 'event_id',
            'foreighColumn' => 'id'
        ),
        'User' => array (
            'className' => 'UserModel',
            'referenceColumn' => 'user_id',
            'foreignColumn' => 'id'
        )
    );
}
