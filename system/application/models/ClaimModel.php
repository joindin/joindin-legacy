<?php
/**
 * Class ClaimModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** SessionModel */
require_once BASEPATH . 'application/models/SessionModel.php';
/** SpeakerProfileModel */
require_once BASEPATH . 'application/models/SpeakerProfileModel.php';
/** TalkModel */
require_once BASEPATH . 'application/models/TalkModel.php';

/**
 * Represents a claim made for a session.
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class ClaimModel extends DomainModel
{
    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'session_claims';
    
    /**
     * @see DomainModel::$_rules
     */
    protected $_rules = array (
        'speaker_profile_id' => 'required',
        'session_id' => 'required',
        'data' => 'required'
    );
    
    /**
     * @see DomainModel::$_hasOne
     */
    protected $_hasOne = array (
        'Speaker' => array (
            'className' => 'SpeakerProfileModel',
            'referenceColumn' => 'speaker_profile_id',
            'foreignColumn' => 'id'
        ),
        'Session' => array (
            'className' => 'SessionModel',
            'referenceColumn' => 'session_id',
            'foreignColumn' => 'id'
        )
    );
    
    /***/

    /**
     * Returns the title of the session that this claim is for.
     * @return string
     */
    public function getSessionTitle()
    {
        return $this->getSession()->getTitle();
    }
    
    /**
     * Returns the name of the speaker that made this claim.
     * @return string
     */
    public function getSpeakerName()
    {
        return $this->getSpeaker()->getFullName();
    }
    
    /**
     * Returns the talk the session was claimed to.
     * @return TalkModel
     */
    public function getTalk()
    {
        if(!empty($this->_data['talk_id'])) {
            $dao = new TalkModel();
            $talk = $dao->find($this->_data['talk_id'], true);
            if(null !== $talk) {
                return $talk;
            }
        }
        
        return null;
    }

    /** 
     * Returns the title of the talk this claim is for.
     * @return string
     */
    public function getTalkTitle() 
    {
        $title = '';
        if($this->getTalk() !== null) {
            $title = $this->getTalk()->getTitle();
        }
        
        return $title;
    }
    
    /**
     * Approves the claim. If no talk_id was provided a new talk will be created 
     * for the speaker making the claim.
     * @return boolean
     */
    public function approve()
    {
        
        $session = $this->getSession();
        
        if(empty($this->_data['talk_id'])) {
            $speaker = $this->getSpeaker();
            // create a new talk
            $talk = new TalkModel(array(
                'speaker_profile_id' => $speaker->getId(),
                'title' => $session->getTitle(),
                'description' => $session->getDescription(),
                'active' => 1
            ));
            
            $id = $talk->save();
            $this->_data['talk_id'] = $id;
        }
    
        $session->setTalkId($this->_data['talk_id']);
        
        if($session->save()) {
            return $this->delete();
        }
    }
    
}
