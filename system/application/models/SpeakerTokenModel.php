<?php
/**
 * Class SpeakerTokenModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** SpeakerModel */
require_once BASEPATH . 'application/models/SpeakerModel.php';

/**
 * A token that exposes fields from a speaker profile.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class SpeakerTokenModel extends DomainModel
{
    /** 
     * @see DomainModel::$_table
     */
    protected $_table = 'speaker_tokens';
    
    /**
     * @see DomainModel::$_belongsTo
     */
    protected $_belongsTo = array (
        'Speaker' => array (
            'className' => 'SpeakerModel',
            'referenceColumn' => 'speaker_profile_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * The fields that are exposed by this token.
     * @var array
     */
    protected $_fields = null;
    
    /**
     * All token strings stored in the database without their context data.
     * @param array
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
     * Returns the fields that are exposed by this token.
     * @return array
     */
    public function getFields()
    {
        if(null === $this->_fields) {
            $this->_fetchFields();
        }
        
        return $this->_fields;
    }
    
    /**
     * Collects the field names that are exposed by this token.
     */
    protected function _fetchFields()
    {
        $fields = array();
        
        $sql = "SELECT `field_name` FROM `speaker_token_fields` WHERE `speaker_token_id` = '{$this->getId()}';";
        $query = $this->_database->query($sql);
        foreach($query->result() as $row) {
            $fields[] = $row->field_name;
        }
        
        $this->_fields = $fields;
    }
    
    /**
     * Sets the fields that are exported by this token.
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->_fields = $fields;
    }
    
    /**
     * Saves the token fields to the database. This takes the simple (but a bit
     * dangerous) approach of first deleting all existing fields for this token
     * and then inserting the selected fields.
     */
    protected function _saveFields()
    {
        // Delete old fields
        $sql = "DELETE FROM `speaker_token_fields` WHERE `speaker_token_id` = '{$this->getId()}';";
        $deleteQuery = $this->_database->query($sql);
        
        if($deleteQuery && count($this->_fields) > 0) {
            // Save new fields
            $sql = "INSERT INTO `speaker_token_fields` (`speaker_token_id`, `field_name`) VALUES ";
            $current = 0;
            foreach($this->_fields as $field) {
                $sql .= "('{$this->getId()}', '{$field}')";
                if($current != (count($this->_fields) -1)) {
                    $sql .= ', ';
                }
                $current++;
            }
            $sql .= ';';
            $insertQuery = $this->_database->query($sql);
            return $insertQuery;
        }
        
        return $deleteQuery;
    }
    
    /**
     * Prepares the fields for insertion in the database
     * @param string $item
     * @param int $inde
     */
    protected function _prepareInsertField(&$item, $index) {
        $item = "('{$this->getId()}', '{$item}')";
    }
    
    /**
     * @see DomainModel::postSave()
     */
    protected function postSave($success) {
        if($success) {
            $this->_saveFields();
        }
    }
}
