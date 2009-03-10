<?php
/**
 * Class Getdetail
 */

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/service/ServiceHandler.php';
/** Profile_token_model */
require_once BASEPATH . 'application/models/profile_token_model.php';
/** ServiceReponseXml */
require_once BASEPATH . 'application/libraries/service/ServiceResponseXml.php';
/** ServiceReponseJson */
require_once BASEPATH . 'application/libraries/service/ServiceResponseJson.php';

/**
 * Returns the speaker details for a token.
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
        
        $dao = new Profile_token_model();
        $tokenModel = $dao->findByAccessToken($token);
        
        if(is_null($tokenModel)) {
            // Return an error
            $this->_sendError('Token not found', 400);
        }
        
        if($this->_outputType == 'xml') {
            $response = new ServiceResponseXml();
        } else {
            $response = new ServiceResponseJson();
        }
        $response->addArray($tokenModel->getProfileData(), 'speaker');
        
        // Return the response
        return $response->getResponse();
    }
    
}

