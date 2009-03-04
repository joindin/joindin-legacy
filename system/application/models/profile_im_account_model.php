<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_im_account_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** Profile_im_account_network_model */
require_once BASEPATH . 'application/models/profile_im_account_network_model.php';

/**
 * Represents a profile's instant messenger account.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class Profile_im_account_model extends DomainModel 
{
    protected $_table = 'profile_im_accounts';
    
    protected $_rules = array (
    	'profile_id' => 'required',
    	'network_name' => 'required',
    	'account_name' => 'required'
    );
    
    /**
     * Protocol this im account uses
     * @var Profile_im_account_network
     */
    protected $_accountNetwork = null;
    
    /**
     * Returns the protocol this im account uses.
     * @return string
     */
    public function getProtocol()
    {
        if(!is_null($this->_accountNetwork) && ($this->_accountNetwork instanceof Profile_im_account_network_model)) {
            return $this->_accountNetwork->getName();
        }
        
        if($this->getImNetworkId() == 0) {
            return 'Unknown';
        }
        
        $this->_accountNetwork = new Profile_im_account_network_model($this->getImNetworkId());
        
        return $this->_accountNetwork->getName();
    }
    
}