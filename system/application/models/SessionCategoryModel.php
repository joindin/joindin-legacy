<?php
/**
 * Class SessionCategoryModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Represents a category for a session
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class SessionCategoryModel extends DomainModel
{
    /**
     * {@see DomainModel::$_table}
     */
    protected $_table = 'session_categories';
}
