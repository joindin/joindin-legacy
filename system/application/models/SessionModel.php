<?php
/**
 * Class SessionModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** LanguageModel */
require_once BASEPATH . 'application/models/LanguageModel.php';
/** CategoryModel */
require_once BASEPATH . 'application/models/CategoryModel.php';
/** SessionCommentModel */
require_once BASEPATH . 'application/models/SessionCommentModel.php';
/** EventModel */
require_once BASEPATH . 'application/models/EventModel.php';
/** TalkModel */
require_once BASEPATH . 'application/models/TalkModel.php';

/**
 * Represents a session given at an event.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class SessionModel extends DomainModel
{
    
    protected $_table = 'sessions';
    
    protected $_rules = array (
        'title' => array('trim', 'required'),
        'description' => array('trim', 'required'),
        'speaker' => array('trim', 'required'),
        'date' => array('required', 'timestamp', 'validate_inside_event')
    );
    
    protected $_hasMany = array(
        'Comments' => array (
            'className' => 'SessionCommentModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'session_id',
            'cascadeOnDelete' => true,
        )
    );
    
    protected $_hasOne = array (
        'Category' => array (
            'className' => 'CategoryModel',
            'referenceColumn' => 'category_id',
            'foreignColumn' => 'id',
        ),
        'Talk' => array (
            'className' => 'TalkModel',
            'referenceColumn' => 'talk_id',
            'foreignColumn' => 'id'
        ),
        'Language' => array (
            'className' => 'LanguageModel',
            'referenceColumn' => 'language_id',
            'foreignColumn' => 'id'
        )
    );
    
    protected $_belongsTo = array (
        'Event' => array (
            'className' => 'EventModel',
            'referenceColumn' => 'event_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * The avarage rating for this Session
     * @var int
     */
    protected $_rating = null;
    
    /**
     * List of all claim tokens.
     * @var array
     */
    protected $_claimTokens = null;
    
    /** **/
    
    /**
     * Returns a list of the most popular sessions (e.g. with the most comments).
     * @param int|string $limit
     * @return mixed
     */
    public function getPopularSessions($limit = null)
    {
        $popular = array();
        // Construct the query
        $query = "SELECT `sessions`.*, COUNT(`session_comments`.`id`) AS `comment_count` " .
                 "FROM `sessions`, `session_comments` " .
                 "WHERE `session_comments`.`session_id` = `sessions`.`id` " .
                 "GROUP BY `sessions`.`id` ".
                 "ORDER BY `comment_count` DESC ";
                 
        if(!is_null($limit) && is_numeric($limit)) {
            $query .= "LIMIT {$limit}";
        }
        
        $result = $this->_database->query($query);
        
        foreach($result->result_array() as $row) {
            $popular[] = new SessionModel($row);
        }
        
        return $popular;
    }
    
    /**
     * Checks if the session is open for comments. Comments can only be added the day 
     * after the session was given (local time).
     * @return boolean
     */
    public function isOpenForComments()
    {
        $gmtTime = mktime(gmdate('h'), gmdate('i'), gmdate('s'), gmdate('m'), gmdate('d'), gmdate('Y'));
        $localTime = $gmtTime + (3600 * ((int) $this->getTimezone()));
        
        if($localTime > $this->getDate()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the number of comments related to this Session
     * @return int
     */
    public function getCommentCount()
    {
        return count($this->getComments());
    }
    
    /**
     * Returns the rating for this Session, calculated from the comments.
     * @return int
     */
    public function getRating()
    {
        if(is_null($this->_rating)) {
            $this->_calculateRating();
        }
        
        return $this->_rating;
    }

    /**
     * Returns the type of sessions
     * @return string
     * @todo implement body
     */
    public function getType()
    {
        //return $this->getCategory()->getTitle();
        return 'Talk';
    }

    /**
     * Returns the id of the event this session belongs to.
     * @return int
     */
    public function getEventId() {
        return $this->getEvent()->getId();
    }
    
    /**
     * Returns the title of the event this session belongs to.
     * @return string
     */
    public function getEventTitle() {
        return $this->getEvent()->getTitle();
    }
    
    /**
     * Returns the name of the speaker. If a talk is connected to the session 
     * the speaker name will be fetched from that talks speaker model. Else 
     * the text field from this Session will be used.
     * @return string
     */
    public function getSpeaker()
    {
        $speaker = $this->_getRelation('speaker');

        if(null === $speaker) {
            $speaker = $this->_data['speaker_name'];
        }
    
        return $speaker;
    }
    
    /**
     * Sets the session date as a string. Date must be in format %m/%d/%Y to be 
     * able to be parsed by strptime().
     * @param string $date
     */
    public function setDateString($date)
    {
        if(!empty($date)) {
            $dateParts = strptime($date, '%m/%d/%Y');
            $timestamp = mktime(0, 0, 0, $dateParts['tm_mon'] + 1, $dateParts['tm_mday'], $dateParts['tm_year'] + 1900);
            $this->setDate($timestamp);
        }
    }
    
    /**
     * Validates a timestamp to check if it's a valid date.
     *
     * @param int $value the timestamp to check
     * @return boolean
     */
    public function validate_inside_event($field, $value)
    {
        // Get the dates to compare
        $sessionDate = $this->_get('date');
        $eventStart = $this->getEvent()->getStart();
        $eventEnd = $this->getEvent()->getEnd();
        
        // Sanitize dates (make sure time is stripped off)
        $sessionDate = mktime(0, 0, 0, date('m', $sessionDate), date('d', $sessionDate), date('Y', $sessionDate));
        $eventStart = mktime(0, 0, 0, date('m', $eventStart), date('d', $eventStart), date('Y', $eventStart));
        $eventEnd = mktime(0, 0, 0, date('m', $eventEnd), date('d', $eventEnd), date('Y', $eventEnd));
        
        // Add a proper error message for this validation rule
        $this->_validator->addErrorMessage('validate_inside_event', 'The session date must be inside the Events date range.');
        
        return ($sessionDate >= $eventStart && $sessionDate <= $eventEnd);
    }
    
    /**
     * Checks if this session is claimed by a speaker. If it is claimed the session 
     * is connected to the talk of a speaker thus the `talk_id` column won't be NULL.
     * @return boolean
     */
    public function isClaimed()
    {
        return ($this->_get('talk_id') !== null && $this->_get('talk_id') != 0);
    }
    
    /**
     * Activates the session
     */
    public function activate()
    {
        $this->_set('active', 1);
        $this->save();
    }
    
    /**
     * Deactivates the sessions.
     */
    public function deactivate()
    {
        $this->_set('active', 0);
        $this->save();
    }
    
    /**
     * Returns a list of all string tokens in use as claim token.
     * @return array
     */
    public function getAllStringTokens()
    {
        if(null === $this->_claimTokens) {
            
            $sql = "SELECT `claim_token` FROM `{$this->_table}` WHERE `claim_token` != '' AND `claim_token` != NULL";
            $query = $this->_database->query($sql);
            $tokens = array();
            foreach($query->result() as $row) {
                $tokens[$row->claim_token] = $row->claim_token;
            }
            
            $this->_claimTokens = $tokens;
        }
        
        return $this->_claimTokens;
    }
    
    /**
     * Calculates the rating based on the comments for this Session.
     */
    protected function _calculateRating()
    {
        $rating = 0;
        if($this->getCommentCount() > 0) {
            foreach($this->getComments() as $comment) {
                $rating = $rating + $comment->getRating();
            }
            
            $rating = round($rating / $this->getCommentCount());
        }
        
        $this->_rating = $rating;
    }
    
}
