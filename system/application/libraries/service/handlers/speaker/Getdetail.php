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
        
        $xmlResponse = new ServiceResponseXml();
        
        if(!is_null($tokenModel)) {
            // Add the data to the response
            $xmlResponse->addArray($tokenModel->getProfileData(), 'speaker');
        } else {
            // Return an error
            $this->_statusCode = 400;
            $xmlResponse->addString('No data found for token', 'error');
        }
        
        // Return the response
        return $xmlResponse->getResponse();
    }
    
}

