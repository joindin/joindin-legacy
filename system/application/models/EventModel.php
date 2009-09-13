<?php
/**
 * Class EventModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** SessionModel */
require_once BASEPATH . 'application/models/SessionModel.php';
/** EventCommentModel */
require_once BASEPATH . 'application/models/EventCommentModel.php';
/** UserModel */
require_once BASEPATH . 'application/models/UserModel.php';
/** AttendanceModel */
require_once BASEPATH . 'application/models/AttendanceModel.php';

/**
 * Represents an event
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class EventModel extends DomainModel 
{

    /**
     * @see DomainModel
     */
    protected $_table = 'events';
    
    /**
     * @see DomainModel
     */
    protected $_rules = array(
        'title' => array('required', 'alphanumeric'),
        'description' => array('required', 'strip_html'),
        'start' => array('required', 'timestamp'),
        'end' => array('required', 'timestamp'),
        'stub' => array('validate_stub'),
        'contact_name' => array('trim', 'required', 'strip_html'),
        'contact_email' => array('trim', 'required', 'email')
    );
    
    /**
     * @see DomainModel
     */
    protected $_hasMany = array(
        'Sessions' => array (
            'className' => 'SessionModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'event_id',
            'cascadeOnDelete' => true,
        ),
        'Comments' => array (
            'className' => 'EventCommentModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'event_id',
            'cascadeOnDelete' => true,
            'orderBy' => 'date ASC',
        ), 
    );
    
    /**
     * Managers for this event.
     * @var array
     */
    protected $_managers = null;
    
    /**
     * The users attending this event
     * @var array
     */
    protected $_attendees = null;
    
    /**
     * The total number of comments made for this event.
     * @var int
     */
    protected $_totalCommentCount = null;
    
    /** **/
    
    /**
     * Returns a list of Events with the highest user attendance. A limit can 
     * be supplied to control the number of Events returned.
     * @param int $limit
     * @return mixed
     */
    public function getHotEvents($limit = null)
    {
        $hotEvents = array();
        
        $query = "SELECT `events`.`id`, (SELECT COUNT(`attendance`.`id`) FROM `attendance` WHERE `attendance`.`event_id` = `events`.`id`) AS `attendance_count` " .
                 "FROM `events` " .
                 "WHERE (`events`.`pending` IS NULL OR `events`.`pending` = '0') " .
                 "AND (`events`.`active` = '1') " .
                 "AND `events`.`end` < '" . time() . "' " .
                 "ORDER BY `attendance_count`, `events`.`start` DESC";
        
        if(null !== $limit) {
            $query .= ' LIMIT ' . $limit;
        }
        
        $result = $this->_database->query($query);
        foreach($result->result_array() as $row) {
            
            $hotEvents[] = $this->create($row);
        }
        
        return $hotEvents;
    }
    
    /**
     * Returns a list of upcoming Events. A limit can be supplied to control 
     * the number of Events returned.
     * @param boolean $includeNowTakingPlace
     * @param int $limit
     * @return mixed
     */
    public function getUpcomingEvents($includeNowTakingPlace = false, $limit = null)
    {
        $upcomingEvents = array();
        $now = time();
        
        // Construct the WHERE clause
        $where = "(`start` > '{$now}')";
        if($includeNowTakingPlace) {
            $where = substr($where, 0, strlen($where) - 1) . " OR `end` >= '{$now}')";
        }
        $where .= " AND (`pending` IS NULL OR `pending` = '0') AND (`active` = '1')";
        
        $upcomingEvents = $this->findAll($where, '`start` ASC', $limit);
        
        return $upcomingEvents;
    }
    
    /**
     * Returns the a list of Events that are in the past. A limit can be 
     * supplied to control the number of Events returned.
     * @param int $limit
     * @return mixed
     */
    public function getPastEvents($limit = null)
    {
        $pastEvents = array();
        $now = time();
        
        // Construct the WHERE clause
        $where = "(`end` < '{$now}') AND (`pending` IS NULL or `pending` = '0') AND (`active` = '1')";
        $pastEvents = $this->findAll($where, '`end` DESC', $limit);
        
        return $pastEvents;
    }
    
    /**
     * Returns the events for given date range.
     * @param int $rangeStart
     * @param int $rangeEnd
     * @return array
     */
    public function getEventsForDateRange($rangeStart, $rangeEnd)
    {
        $this->log("Getting events for range " . date('d/M/Y G:i:s', $rangeStart) . "({$rangeStart}) - " . date('d/M/Y G:i:s', $rangeEnd) . "({$rangeEnd})");
    
        // WHERE clause and query
        $where = "(" . 
            "(`start` >= '{$rangeStart}' AND `start` <= '{$rangeEnd}') " . // <-- events that start within the range
            "OR (`end` >= '{$rangeStart}' AND `end` <= '{$rangeEnd}') " . // <-- events that end within the range
            "OR (`start` <= '{$rangeStart}' AND `end` >= '{$rangeEnd}') " . // <-- events that start and end within the range
            ") " .
            "AND (`active` = '1')"; // <-- only include active events
        $events = $this->findAll($where, '`start` DESC');
        
        return $events;
    }
    
    /**
     * Returns the events with a pending status.
     * @return array
     */
    public function getPendingEvents()
    {
        $events = $this->findByPending(1);
        
        return $events;
    }
    
    /**
     * Returns the number of events per month
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getEventCountPerMonthDay($month, $year = null)
    {
        // Month boudaries
        $monthStart = mktime(0, 0, 0, $month, 1, $year);
        $monthEnd = mktime(0, 0, 0, $month + 1, 0, $year);
        // The next day in seconds
        $nextDay = 86400;
            
        // Get the events for the month
        $events = $this->getEventsForDateRange($monthStart, $monthEnd);
        $dates = array();
        foreach($events as $event) {
            // clean event start and end date
            $eventStartDay = mktime(0, 0, 0, date('n', $event->getStart()), date('j', $event->getStart()), date('Y', $event->getStart()));
            $eventEndDay = mktime(0, 0, 0, date('n', $event->getEnd()), date('j', $event->getEnd()), date('Y', $event->getEnd()));
            
            // Loop the event day (until we reach the end of the month)
            for ($i = $eventStartDay; $i <= $eventEndDay && $i <= $monthEnd; $i += $nextDay) {
        	    $date = date('Y-m-d', $i);
        	    if(!isset($dates[$date])) {
        	        $dates[$date] = 0;
        	    }
        	    $dates[$date]++;
        	}
        }
        
        return $dates;
    }
    
    /**
     * Returns the number of sessions given at the event.
     * @return int
     */
    public function getSessionCount()
    {
        return count($this->getSessions());
    }
    
    /**
     * Returns the number of comments for this Event.
     * @return int
     */
    public function getCommentCount()
    {
        return count($this->getComments());
    }
    
    /**
     * Returns the total number of comments for this event. This is made up of
     * the event- and sessioncomments.
     * @return int
     */
    public function getTotalCommentCount()
    {
        if(null === $this->_totalCommentCount) {
            $commentCount = $this->getCommentCount();
            foreach($this->getSessions() as $session) {
                $commentCount = $commentCount + $session->getCommentCount();
            }
            $this->_totalCommentCount = $commentCount;
        }
        return $this->_totalCommentCount;
    }
    
    /**
     * Returns the attendees for this event.
     * @return array
     */
    public function getAttendees()
    {
        if(null === $this->_attendees) {
            $this->_fetchAttendees();
        }
        return $this->_attendees;
    }
    
    /**
     * Fetches the attendees from the database.
     */
    protected function _fetchAttendees()
    {
        // select the user id's from the attendance table
        $result = $this->_database->query("SELECT `user_id` FROM `attendance` WHERE `event_id` = '{$this->getId()}'");

        $userIds = array();
        foreach($result->result_array() as $row) {
            $userIds[] = $row['user_id'];
        }
        
        if(count($userIds) > 0) {
            // fetch the users
            $userWhere = '`id` IN(' . implode(',', $userIds) . ')';
            $userDao = new UserModel();
            $attendees = $userDao->findAll($userWhere);
        } else {
            $attendees = array();
        }
        
        $this->_attendees = $attendees;
    }
    
    /**
     * Returns the number of users attending.
     * @return int
     */
    public function getAttendanceCount()
    {
        return count($this->getAttendees());
    }
    
    /**
     * Checks if a user is attending this Event.
     * @param int|string $userId
     */
    public function userIsAttendee($userId) 
    {
        if(!is_numeric($userId)) {
            return false;
        }
        
        foreach($this->getAttendees() as $attendee) {
            if($attendee->getUserId() == $userId) {
                return true;
            }
        }
    }
    
    /**
     * Checks if the user is a speaker at the event.
     * @param string $userId
     * @return boolean
     * @todo implement this function
     */
    public function userIsSpeaker($userId)
    {
        return false;
    }
    
    /**
     * Sets the start date of the event as a string. The string needs to be in 
     * the %m/%d/%Y format to be compatible with strptime(). The date will be 
     * converted to a timestamp and save in the start column.
     * @param string $date
     */
    public function setStartString($start)
    {
        if(!empty($start)) {
            $dateParts = strptime($start, '%m/%d/%Y');
            $timestamp = mktime(0, 0, 0, $dateParts['tm_mon'] + 1, $dateParts['tm_mday'], $dateParts['tm_year'] + 1900);
            $this->setStart($timestamp);
        }
    }
    
    /**
     * Sets the end date of the event as a string. The string needs to be in 
     * the %m/%d/%Y format to be compatible with strptime(). The date will be 
     * converted to a timestamp and save in the end column.
     * @param string $date
     */
    public function setEndString($end)
    {
        if(!empty($end)) {
            $dateParts = strptime($end, '%m/%d/%Y');
            $timestamp = mktime(0, 0, 0, $dateParts['tm_mon'] + 1, $dateParts['tm_mday'], $dateParts['tm_year'] + 1900);
            $this->setEnd($timestamp);
        }
    }
    
    /**
     * Checks if the event is active.
     * @return boolean
     */
    public function isActive()
    {
        return ($this->_get('active') == 1);
    }
    
    /**
     * Returns if the event has status pending
     * @return boolean
     */
    public function isPending()
    {
        return ($this->_data['pending'] == 1);
    }

    /**
     * Returns the managers for this event.
     * @return array
     */
    public function getManagers()
    {
        if(null === $this->_managers) {
            $this->_fetchManagers();
        }
        
        return $this->_managers;
    }

    /**
     * Fetches the managers for this event from the database.
     */
    protected function _fetchManagers()
    {
        // select the user id's from the event_managers table
        $result = $this->_database->query("SELECT `user_id` FROM `event_managers` WHERE `event_id` = '{$this->getId()}'");

        $userIds = array();
        foreach($result->result_array() as $row) {
            $userIds[] = $row['user_id'];
        }
        
        if(count($userIds) > 0) {
            // fetch the users
            $userWhere = '`id` IN(' . implode(',', $userIds) . ')';
            $userDao = new UserModel();
            $managers = $userDao->findAll($userWhere);
        } else {
            $managers = array();
        }
        
        $this->_managers = $managers;
    }

    /**
     * Checks if the specified user is an event manager.
     * @param int $userId
     * @return boolean
     */
    public function isEventManager($userId)
    {
        if(!is_numeric($userId)) {
            return false;
        }
        
        foreach($this->getManagers() as $user) {
            if($user->getId() == $userId) {
                return true;
            }
        }
    }
    
    /**
     * Adds a manager to the event. The is immediately saved to the database.
     * @param UserModel $user
     * @return boolean
     */
    public function addEventManager(UserModel $user)
    {
        // return if the user is already in the managers list
        if($this->isEventManager($user->getId())) {
            return true;
        }
        
        $sql = sprintf("INSERT INTO `event_managers` (`user_id`, `event_id`) VALUES (%s, %s);",
                    $this->_database->escape($user->getId()),
                    $this->_database->escape($this->getIdentifier())
                );
        $query = $this->_database->query($sql);
        
        if($query) {
            $this->_managers[] = $user;
        }
        
        return $query;
    }
    
    /**
     * Removes a manager from the event. This is immediately saved to the database.
     * @param int|UserModel $user_id
     */
    public function removeEventManager($user_id)
    {
        if($user_id instanceof UserModel) {
            $user_id = $user_id->getIdentifier();
        }
        
        foreach($this->_managers as $manager) {
            if($manager->getId() == $user_id) {
                $sql = sprintf("DELETE FROM `event_managers` WHERE `user_id` = %s AND `event_id` = %s LIMIT 1;",
                            $this->_database->escape($user_id),
                            $this->_database->escape($this->getIdentifier())
                        );
                $query = $this->_database->query($sql);
                if($query){
                    unset($this->_managers[key($this->_managers)]);
                }
                return $query;
            }
        }
        
        return true;
    }

    /**
     * Approves the event removing its pending status
     */
    public function approve()
    {
        $this->_set('pending', 0);
        $this->save();
    }

    /**
     * Deactivates the event, its sessions and its comments.
     */
    public function deactivate()
    {
        // Deactivate the event
        $this->_set('active', 0);
        $this->save();
        
        // Deactivate event sessions
        foreach($this->getSessions() as $session) {
            $session->deactivate();
        }
    }
    
    /**
     * Validates a stub value if one is provided. A stub should only contain 
     * alphanumeric values and must not be longer than 15 characters. It must 
     * also be unique.
     * @param string $field
     * @param string $param
     * @return boolean
     */
    public function validate_stub($field, $param)
    {
        $stub = $this->_get($field);
        if(empty($stub)) {
            return true;
        }
        
        if($this->isNew()) {
            if(strlen($stub) > 15) {
                return false;
            }
            
            $queryResult = $this->_database->query("SELECT `id`, `stub` FROM `{$this->_table}`");
            
            $stubs = array();
            foreach($queryResult->result_array() as $row) {
                $stubs[$row['id']] = strtolower($row['stub']);
            }
            
            // Add an appropriate error message
            $this->_validator->addErrorMessage('validate_stub', 'That stub is already registered.');
            
            return !in_array($stub, $stubs);
        }
        
        return true;
    }
}
