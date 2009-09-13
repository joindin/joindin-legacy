<?php
/**
 * Class TalkModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** SpeakerProfileModel */
require_once BASEPATH . 'application/models/SpeakerProfileModel.php';
/** SessionModel */
require_once BASEPATH . 'application/models/SessionModel.php';
/** TalkTokenModel */
require_once BASEPATH . 'application/models/TalkTokenModel.php';

/**
 * Represents a talk owned by a speaker.
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class TalkModel extends DomainModel 
{

    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'talks';

    /**
     * @see DomainModel::$_hasOne
     */
    protected $_hasOne = array (
        'Speaker' => array (
            'className' => 'SpeakerProfileModel',
            'referenceColumn' => 'speaker_profile_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * @see DomainModel::$_hasMany
     */
    protected $_hasMany = array (
        'Sessions' => array (
            'className' => 'SessionModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'talk_id'
        ),
        'Tokens' => array (
            'className' => 'TalkTokenModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'talk_id'
        )
    );
    
    /**
     * The combined rating for this talk.
     * @var int
     */
    protected $_rating = null;
    
    /** **/
    
    /**
     * Returns the id from the speaker owning this talk.
     * @return int|null
     */
    public function getSpeakerId()
    {
        if(null !== $this->getSpeaker()) {
            return $this->getSpeaker()->getId();
        }
    }
    
    /**
     * Returns thecombined rating for this talk.
     * @return int
     */
    public function getRating()
    {
        if(null === $this->_rating) {
            $rating = 0;
            $sessions = $this->getSessions();
            if(count($sessions) > 0) {
                foreach($sessions as $session) {
                    $rating = $rating + $session->getRating();
                }
                $rating = round($rating / count($sessions));
            }
            $this->_rating = $rating;
        }
        return $this->_rating;
    }
    
    /**
     * Returns the number of sessions connected to this talk.
     * @return int
     */
    public function getSessionCount()
    {
        return count($this->getSessions());
    }
    
    /**
     * Returns the number of tokens for this talk.
     * @return int
     */
    public function getTokenCount()
    {
        return count($this->getTokens());
    }
    
    /**
     * Resets sessions claimed to this talk.
     * @param boolean $success
     */
    protected function postDelete($success)
    {
        if($success && $this->getIdentifier() != '') {
            $sql = "UPDATE `sessions` SET `talk_id` = NULL WHERE `talk_id` = '{$this->getIdentifier()}';";
            $query = $this->_database->query($sql);
        }
    }

}
