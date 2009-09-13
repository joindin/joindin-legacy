<?php
/**
 * Class UserModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** SpeakerProfileModel */
require_once BASEPATH . 'application/models/SpeakerProfileModel.php';
/** SessionCommentModel */
require_once BASEPATH . 'application/models/SessionCommentModel.php';
/** EventCommentModel */
require_once BASEPATH . 'application/models/EventCommentModel.php';

/**
 * Represents a User in the application.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class UserModel extends DomainModel
{
    
    /**
     * @see DomainModel:$_table
     */
    protected $_table = 'users';
    
    /**
     * @see DomainModel:$_hasMany
     */
    protected $_hasMany = array (
        'SessionComments' => array (
            'className' => 'SessionCommentModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'user_id'
        ),
        'EventComments' => array (
            'className' => 'EventCommentModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'user_id'
        ),
        'Attendance' => array (
            'className' => 'AttendanceModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'user_id'
        )
    );
    
    /**
     * @see DomainModel:$_hasOne
     */
    protected $_hasOne = array (
        'SpeakerProfile' => array (
            'className' => 'SpeakerProfileModel',
            'referenceColumn' => 'id', 
            'foreignColumn' => 'user_id',
            'cascadeOnDelete' => true
        )
    );
    
    /**
     * @see DomainModel:$_rules
     */
    protected $_rules = array (
        'username' => array('required', 'strtolower', 'validate_unique_username'),
        'password' => array('required'),
        'email' => array('required', 'valid_email', 'validate_unique_email'),
        'display_name' => array('required'),
    );
    
    /** **/
    
    /**
     * Overrides the public method.
     * @param int|string $id
     */
    protected function setId($id)
    {
        $this->_set('id', $id);
    }
    
    /**
     * Returns the name for this user. If a display name is not provided the 
     * less friendly username will be returned
     * @return string
     */
    public function getName()
    {
        $displayName = $this->_get('display_name');
        if(!empty($displayName)) {
            return $displayName;
        }
        else {
            return $this->_get('username');
        }
    }
    
    /**
     * Checks if the user had admin rights.
     * @return boolean
     */
    public function isAdmin()
    {
        return ($this->getAdmin() == 1);
    }
    
    /**
     * Checks if the user account is active
     * @return boolean
     */
    public function isActive()
    {
        return ($this->getActive() == 1);
    }
    
    /**
     * Checks if the user has a speaker profile.
     * @return boolean
     */
    public function hasSpeakerProfile()
    {
        return ($this->getSpeakerProfile() !== null);
    }
    
    /**
     * Returns the sessions this user has given. This is based on the sessions 
     * connected to the talks belonging to the speaker profile of this user.
     * @return array
     * @todo implement body
     */
    public function getSessions()
    {
        return array();
    }
    
    /**
     * Checks if an username is unique
     * @param string $field
     * @param string $value
     * @return boolean
     */
    public function validate_unique_username($field, $value)
    {
        $usersnames = array();
        $queryResult = $this->_database->query("SELECT `{$this->getIdentifierField()}`, `username` FROM `{$this->_table}`");
        
        foreach($queryResult->result_array() as $row) {
            $usernames[$row[$this->getIdentifierField()]] = strtolower($row['username']);
        }
        
        // Add an appropriate error message
        $this->_validator->addErrorMessage('validate_unique_username', 'That Username is already taken.');
        
        $id = $this->getIdentifier();
        $username = $this->_data['username'];
        
        /*
         * If the username is in the array, and the id connected to that username 
         * is not the id from this user, FAIL!
         */
        return !(in_array(strtolower($username), $usernames) && (array_search($username, $usernames) != $id));
    }
    
    /** 
     * Checks if an email address is unique
     * @param string $field
     * @param string $value
     * @return boolean
     */
    public function validate_unique_email($field, $value)
    {
        $emailAddresses = array();
        $queryResult = $this->_database->query("SELECT `{$this->getIdentifierField()}`, `email` FROM `{$this->_table}`");
        
        foreach($queryResult->result_array() as $row) {
            $emailAddresses[$row[$this->getIdentifierField()]] = strtolower($row['email']);
        }
        
        // Add an appropriate error message
        $this->_validator->addErrorMessage('validate_unique_email', 'That Email Address is already registered.');
        
        $id = $this->getIdentifier();
        $email = $this->_data['email'];
        
        /*
         * If the email address is in the array, and the id connected to that email address 
         * is not the id from this user, FAIL!
         */
        return !(in_array(strtolower($email), $emailAddresses) && (array_search($email, $emailAddresses) != $id));
    }
    
    /**
     * @see DomainModel::__toString
     */
    public function __toString()
    {
        return ((string) $this->getName());
    }
    
}
