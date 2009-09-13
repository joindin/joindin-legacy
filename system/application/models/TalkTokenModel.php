<?php
/**
 * Class TalkTokenModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** TalkModel */
require_once BASEPATH . 'application/models/TalkModel.php';

/**
 * Represents a token to access talk data.
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class TalkTokenModel extends DomainModel
{
    
    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'talk_tokens';
   
    /**
     * @see DomainModel::$_hasOne
     */
    protected $_hasOne = array (
        'Talk' => array (
            'className' => 'TalkModel',
            'referenceColumn' => 'talk_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * All string tokens in the database.
     * @var array
     */
    protected $_tokens = null;
    
    /** **/
    
    /**
     * Returns all stored token strings without their relevant data.
     * @return array
     */
    public function getAllTokenStrings()
    {
        if(null === $this->_tokens) {
            $query = "SELECT `access_token` FROM `{$this->_table}`";
            $statement = $this->_database->query($query);
            $tokenRows = $statement->result();
            $tokens = array();
            foreach($tokenRows as $row) {
                $tokens[$row->access_token] = $row->access_token;
            }
            $this->_tokens = $tokens;
        }
        
        return $this->_tokens;
    }
    
    /**
     * Returns the id of the talk this token is connected to.
     * @return int
     */
    public function getTalkId()
    {
        if(null !== $this->getTalk()) {
            return $this->getTalk()->getId();
        }
    }
   
}

?>