<?php
/**
 * Categories model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Categories model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Categories_model extends Model
{
    /**
     * Retrieves categories from the database
     *
     * @return mixed
     */
    public function getCats()
    {
        $this->db->from('categories');
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Does nothing. Not implemented
     *
     * @param integer $tid Talk id (void)
     *
     * @return void
     */
    public function getTalkCat($tid)
    {

    }

    /**
     * Associates a talk with a category
     *
     * @param integer $tid Talk id
     * @param integer $cid Category id
     *
     * @return void
     */
    public function setTalkCat($tid, $cid)
    {
        $arr = array(
            'cat_id'  => $cid,
            'talk_id' => $tid
        );
    }

}
