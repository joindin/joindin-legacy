<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_web_address_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** Web_type_model */
require_once BASEPATH . 'application/models/profile_web_address_type_model.php';

/**
 * Represents a web address from a profile.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Profile_web_address_model extends DomainModel 
{
    protected $_table = 'profile_web_addresses';
    
    protected $_rules = array (
    	'profile_id' => 'required',
    	'web_type_id' => 'required',
    	'url' => 'required'
    );
    
    /**
     * The type of web address
     * @var Profile_web_address_type_model
     */
    protected $_webAddressType = null;
    
    /**
     * Returns the web address type
     * @return string
     */
    public function getType()
    {
        if(!is_null($this->_webAddressType) && ($this->_webAddressType instanceof Profile_web_address_type_model)) {
            return $this->_webAddressType->getName();
        }
        
        if($this->getWebTypeId() == 0) {
            return 'Unknown';
        }
        
        $this->_webAddressType = new Profile_web_address_type_model($this->getWebTypeId());
        
        return $this->_webAddressType->getName();
    }
}
