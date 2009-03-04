<?php
/**
 * Class Profile_web_address_type_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * 
 * @author mattijs
 *
 */
class Profile_web_address_type_model extends DomainModel
{
    
    protected $_table = 'profile_web_address_types';
    
    /**
     * Returns all records for this model for use in a dropdown.
     * @return array
     */
    public function getDropdownData()
    {
        $dataArray = $this->_findAll();
        
        $returnData = array();
        foreach($dataArray as $row) {
            $returnData[$row['id']] = $row['name'];
        }
        
        return $returnData;
    }
    
}