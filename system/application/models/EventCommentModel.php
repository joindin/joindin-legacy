<?php
/**
 * Class EventCommmentModel
 * @package Core
 * @subpackage Models
 */

/** CommentModel */
require_once BASEPATH . 'application/models/CommentModel.php';

/**
 * Represents a comment for an event.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class EventCommentModel extends CommentModel
{

    /**
     * @see DomainModel
     */
    protected $_table = 'event_comments';
    
    /**
     * @see DomainModel
     */
    protected $_belongsTo = array (
        'Event' => array (
            'className' => 'EventModel',
            'referenceColumn' => 'event_id',
            'foreignColumn' => 'id',
        )
    );
    
    /**
     * @see DomainModel
     */
    protected $_rules = array(
        'event_id' => array('required', 'numeric'),
        'author_name' => array('strip_tags', 'trim', 'required'),
        'comment' => array('trim', 'strip_tags', 'required'),
        'date' => array('required', 'numeric'),
    );
    
    /**
     * Returns the name of the comment author. If it was an anonymous user the 
     * value from the author_name will be returned. If it was a registered user 
     * the username from that user will be used.
     * @return string
     */
    public function getAuthorName()
    {
        if($this->getUserId() != '' && $this->getUser() instanceof UserModel) {
            return $this->getUser()->getUsername();
        }
        else {
            return $this->_get('author_name');
        }
    }
    
    /**
     * Returns the type of comment.
     * @return string
     */
    public function getType()
    {
        if($this->getEvent()->getStart() > time()) {
            return 'Suggestion';
        } else {
            return 'Feedback';
        }
    }
}

