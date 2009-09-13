<?php
/**
 * Class SpeakerProfileModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** UserModel */
require_once BASEPATH . 'application/models/UserModel.php';
/** CountryModel */
require_once BASEPATH . 'application/models/CountryModel.php';
/** MessagingServiceModel */
require_once BASEPATH . 'application/models/MessagingServiceModel.php';
/** WebserviceModel */
require_once BASEPATH . 'application/models/WebServiceModel.php';
/** TalkModel */
require_once BASEPATH . 'application/models/TalkModel.php';
/** SessionModel */
require_once BASEPATH . 'application/models/SessionModel.php';
/** SpeakerTokenModel */
require_once BASEPATH . 'application/models/SpeakerTokenModel.php';

/**
 * Represents a speaker profile. A speaker profile belongs to a {@see UserModel}.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class SpeakerProfileModel extends DomainModel
{

    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'speaker_profiles';
    
    /**
     * @see DomainModel::$_rules
     */
    protected $_rules = array (
        'full_name' => array('trim', 'required'),
        'contact_email' => array('trim', 'required', 'valid_email')
    );
    
    /**
     * @see DomainModel::$_hasOne
     */
    protected $_hasOne = array (
        'Country' => array (
            'className' => 'CountryModel',
            'referenceColumn' => 'country_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * @see DomainModel::$_hasMany
     */
    protected $_hasMany = array (
        'MessagingServices' => array (
            'className' => 'MessagingServiceModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'speaker_profile_id',
            'cascadeOnDelete' => true
        ),
        'WebServices' => array (
            'className' => 'WebServiceModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'speaker_profile_id',
            'cascadeOnDelete' => true
        ),
        'Talks' => array (
            'className' => 'TalkModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'speaker_profile_id'
        ),
        'Tokens' => array (
            'className' => 'SpeakerTokenModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'speaker_profile_id'
        )
    );
    
    /**
     * @see DomainModel::$_belongsTo
     */
    protected $_belongsTo = array (
        'User' => array (
            'className' => 'UserModel',
            'referenceColumn' => 'user_id',
            'foreignColumn' => 'id',
            'cascadeOnDelete' => false
        )
    );
    
    /**
     * Sessions given by this speaker
     * @var array
     */
    protected $_sessions = null;
    
    /** **/
    
    /**
     * Returns the user id from the user owning this speaker profile.
     * @return int|null
     */
    public function getUserId()
    {
        if(null !== $this->getUser()) {
            return $this->getUser()->getId();
        }
    }
    
    /**
     * Returns a list of session the speaker has given. The list can be limited 
     * with the $limit parameter.
     * @param int $limit
     * @return array
     */
    public function getSessions($limit = null)
    {
        if(null === $this->_sessions) {
            $talkIds = array();
            foreach($this->getTalks() as $talk) {
                $talkIds[] = $talk->getId();
            }
            
            $sessionDao = new SessionModel();
            $where = '`talk_id` IN('. implode(',', $talkIds) .')';
            
            $sessions = $sessionDao->findAll($where, '`date` DESC', $limit);
            
            if(null === $sessions) {
                $sessions = array();
            }
            
            $this->_sessions = $sessions;
        }
        
        return $this->_sessions;
    }
    
    /**
     * Checks if any messaging services are registered with the speaker profile.
     * @return boolean
     */
    public function hasMessagingServices()
    {
        return ($this->getMessagingServicesCount() > 0);
    }
    
    /**
     * Checks if any web services are registered with the speaker profile.
     * @return boolean
     */
    public function hasWebServices()
    {
        return ($this->getWebServicesCount() > 0);
    }
    
    /**
     * Returns the number of messaging services connected to this account.
     * @return int
     */
    public function getMessagingServicesCount() 
    {
        return count($this->getMessagingServices());
    }
    
    /**
     * Returns the number of web services connected to this account.
     * @return int
     */
    public function getWebServicesCount() 
    {
        return count($this->getWebServices());
    }

}
