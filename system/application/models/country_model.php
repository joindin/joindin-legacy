<?php
/**
 * Class Country_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Represents a country
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 *
 */
class Country_model extends DomainModel
{
    
    protected $_table = 'countries';
    
    /**
     * Override the save function
     * @see system/application/libraries/DomainModel#save()
     */
    public function save($data)
    {
        throw new Exception('You can\'t save countries');
    }
    
    /**
     * Override the delete function
     * @see system/application/libraries/DomainModel#delete()
     */
    public function delete()
    {
        throw new Exception('You can\'t delete countries');
    }
    
}