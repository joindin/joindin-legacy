<?php
/**
 * Blog post category model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Blog post category model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Blog_post_cat_model extends Model
{
    /**
     * Retrieves the post categories
     *
     * @param integer $pid Post id
     *
     * @return mixed
     */
    function getPostCats($pid)
    {
        $this->db->select('cat_id, name')
            ->from('blog_post_cat')
            ->join('blog_cats', 'blog_post_cat.cat_id=blog_cats.ID');
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Sets a post category
     *
     * @param integer $pid Post id
     * @param integer $cid Category id
     *
     * @return void
     */
    function setPostCat($pid, $cid)
    {
        //remove any associations we have so far
        $this->db->delete('blog_post_cat', array('post_id' => $pid));

        //now we put the category in
        $arr = array('post_id' => $pid, 'cat_id' => $cid);
        $this->db->insert('blog_post_cat', $arr);
    }
}

