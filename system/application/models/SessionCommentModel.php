<?php
/**
 * Class SessionCommentModel
 * @package Core
 * @subpackage Models
 */

/** CommentModel */
require_once BASEPATH . 'application/models/CommentModel.php';
/** SessionModel */
require_once BASEPATH . 'application/models/SessionModel.php';

/**
 * Represents a comment on a session.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class SessionCommentModel extends CommentModel
{
    /**
     * @see DomainModel
     */
    protected $_table = 'session_comments';
    
    /**
     * @see DomainModel
     */
    protected $_hasOne = array (
        'Session' => array (
            'className' => 'SessionModel',
            'referenceColumn' => 'session_id',
            'foreignColumn' => 'id'
        ),
        'Author' => array (
            'className' => 'UserModel',
            'referenceColumn' => 'user_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * @see DomainModel
     */
    protected $_rules = array (
        'comment' => array('trim', 'strip_tags', 'required'),
    );
    
    /** **/
    
    /**
     * Returns the id for the session this comment is for.
     * @return int
     */
    public function getSessionId()
    {
        return $this->getSession()->getId();
    }
    
    /**
     * Returns the title of the session this comment is for.
     * @return string
     */
    public function getSessionTitle()
    {
        return $this->getSession()->getTitle();
    }
    
    /**
     * Checks if the comment was made anonymous.
     * @boolean
     */
    public function isAnonymous()
    {
        return ($this->_get('user_id') == 0);
    }
    
    /**
     * Returns the name of the author. If the user was logged in when making the 
     * comment the display name from the user account is fetched. If not, the 
     * provided display name is returned.
     * @return string
     */
    public function getAuthorName()
    {
        $author = $this->getAuthor();

        if(null !== $author) {
            return $author->getName();
        }
        
        return $this->_get('author_name');    
    }
    
    /**
     * Checks if the comment was marked private.
     * @return boolean
     */
    public function isPrivate()
    {
        return ($this->_get('private') == 1);
    }
 
    /**
     * Checks if the comment was made by the speaker giving the session.
     * @return boolean
     */
    public function isSpeakerComment()
    {
        if((!$this->isAnonymous()) && ($this->getAuthor()->getId() == $this->getSession()->getSpeakerId())) {
            return true;
        }
        
        return false;
    }
    
}
