<?php
/**
 * Class Getdetail
 */

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/ServiceHandler.php';

/**
 * 
 * @author Mattijs Hoitink
 *
 */
class Getdetail extends ServiceHandler
{
    
    
    public function isAuthorized($authData)
    {
        if(!isset($authData->user) || $authData->user != 'mattijs') {
            return false;
        } else {
            return true;
        }
    }

    public function handle($actionData)
    {
        return array(
            'speaker' => array(
                'id' => 60,
                'full_name' => 'Mattijs Hoitink',
                'contact_email' => 'mattijs@ibuildings.nl'
            )
        );
    }
    
}

