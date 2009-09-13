<?php
/**
 * Class AttendanceModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** UserModel */
require_once BASEPATH . 'application/models/UserModel.php';
/** EventModel */
require_once BASEPATH . 'application/models/EventModel.php';

/**
 * Represents an attendance for an event
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class AttendanceModel extends DomainModel
{
    /**
     * @see DomainModel
     */
    protected $_table = 'attendance';
    
    /**
     * @see DomainModel
     */
    protected $_hasOne = array (
        'Event' => array (
            'className' => 'EventModel',
            'referenceColumn' => 'event_id',
            'foreignColumn' => 'id'
        ),
        'User' => array (
            'className' => 'UserModel', 
            'referenceColumn' => 'user_id',
            'foreignColumn' => 'id'
        )
    );
    
    /** **/
    
    /**
     * Checks if the attendee is also a speaker at the event.
     * @return boolean
     * @todo implement body
     */
    public function isSpeaker()
    {
        return false;
    }
    
}
