<?php
/**
 * Countries model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Countries model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Countries_model extends Model
{

    /**
     * Retrieves countries from the database
     *
     * @return mixed
     */
    public function getCountries()
    {
        $this->db->from('countries');
        $this->db->order_by('name', 'asc');
        $q = $this->db->get();

        return $q->result();
    }

}
