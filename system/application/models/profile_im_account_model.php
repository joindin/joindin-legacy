<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_im_account_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

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
}