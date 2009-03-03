<?php
/**
 * Class Getdetail
 */

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/service/ServiceHandler.php';
/** ServiceTokenAuth */
require_once BASEPATH . 'application/libraries/service/ServiceTokenAuth.php';
/** Profile_token_model */
require_once BASEPATH . 'application/models/profile_token_model.php';

/**
 * Returns the details for a speaker.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class Getdetail extends ServiceHandler
{

    public function isAuthorizedRequest()
    {
        return true;
    }

    public function handle()
    {
        $token = (string) $this->_xmlData->action->token;
        
        $model = new Profile_token_model();
        $tokenModel = $model->findByAccessToken($token);
        
        if(is_null($tokenModel)) {
            $this->_statusCode = 400;
            return array('error' => 'No data found for token');
        }
        
        return array(
            'speaker' => $tokenModel->getProfileData()
        );
    }
    
}

