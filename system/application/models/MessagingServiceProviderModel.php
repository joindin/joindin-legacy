<?php
/**
 * Class MessagingServiceProviderModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Represents the provider of a messaging service.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class MessagingServiceProviderModel extends DomainModel
{
    
    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'messaging_service_providers';
    
}
