<?php
/**
 * Class LanguageModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Represents a language for a session
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class LanguageModel extends DomainModel
{
    /**
     * {@see DomainModel::$_table}
     */
    protected $_table = 'languages';
}
