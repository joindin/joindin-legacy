<?php
/**
 * Class WebServiceProviderModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Represents the provider of a webservice (or website).
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class WebServiceProviderModel extends DomainModel
{
    
    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'web_service_providers';
    
}
