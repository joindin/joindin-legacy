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
/** ServiceXmlReponse */
require_once BASEPATH . 'application/libraries/service/ServiceXmlResponse.php';

/**
 * Returns the speaker details for a token.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class Getdetail extends ServiceHandler
{
    protected $_outputType = 'xml';
    
    public function isAuthorizedRequest()
    {
        return true;
    }

    public function handle()
    {
        $token = (string) $this->_xmlData->action->token;
        
        $dao = new Profile_token_model();
        $tokenModel = $dao->findByAccessToken($token);
        
        $xmlResponse = new ServiceXmlResponse();
        
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

