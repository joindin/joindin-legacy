<?php
/**
 * Timezone model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Timezone model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Tz_model extends Model
{

    /**
     * Retrieve continent information
     *
     * @param integre $cid Continent id
     *
     * @return mixed
     */
    public function getContInfo($cid = null)
    {
        $this->db->select('cont')
            ->distinct()
            ->from('tz')
            ->order_by('cont asc');
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Retrieves area information
     *
     * @param string $cont Continent name
     *
     * @return mixed
     */
    public function getAreaInfo($cont)
    {
        $this->db->select('area')
            ->distinct()
            ->where("cont='" . $cont . "'")
            ->from('tz')
            ->order_by('cont asc');
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Retrieves timezone offset information
     *
     * @return mixed
     */
    public function getOffsetInfo()
    {
        $this->db->select('offset')
            ->distinct()
            ->from('tz')
            ->order_by('offset asc');
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Does nothing. Literally blank
     *
     * @param null $tid Void
     *
     * @return void
     */
    public function getTzInfo($tid = null)
    {

    }
}

