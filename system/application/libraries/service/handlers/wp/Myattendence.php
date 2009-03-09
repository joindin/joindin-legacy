<?php
/**
 * Class Myattendence
 */

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/service/ServiceHandler.php';
/** ServiceAuth */
require_once BASEPATH . 'application/libraries/service/ServiceAuth.php';
/** ServiceResponseXml */
require_once BASEPATH . 'application/libraries/service/ServiceResponseXml.php';
/** User_model */
require_once BASEPATH . 'application/models/user_model.php';
/** User_attend_model */
require_once BASEPATH . 'application/models/user_attend_model.php';

/**
 * Returns the events a user is attending
 * @author Mattijs Hoitink
 */
class Myattendence extends ServiceHandler
{
    
    public function isAuthorizedRequest()
    {
        return ServiceAuth::privateAuth($this->_xmlData);
    }
    
    public function handle()
    {
        $userDao = new User_model();
        $user = $userDao->getUser($this->_xmlData->auth->user);
        
        $user = $user[0];
        
        $attendingDao = new User_attend_model();
        $attending = $attendingDao->getUserAttending($user->ID);
        
        $xmlReponse = new ServiceResponseXml();
        
        foreach($attending as $event) {
            $xmlReponse->addArray((array) $event, 'event');
        }
        
        return $xmlReponse->getResponse();
    }
    
}