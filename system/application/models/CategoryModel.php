<?php
/**
 * Class CategoryModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Category for both Talks and Sessions to show what type it is.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class CategoryModel extends DomainModel
{
    /**
     * {@see DomainModel::$_table}
     */
    protected $_table = 'session_categories';
}

