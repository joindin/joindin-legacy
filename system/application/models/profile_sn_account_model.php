<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_sn_account_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Represents a profile's social network account.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class Profile_sn_account_model extends DomainModel 
{
    protected $_table = 'profile_sn_accounts';
    
    protected $_rules = array (
    	'profile_id' => 'required',
    	'service_name' => 'required',
    	'account_url' => 'required'
    );
}