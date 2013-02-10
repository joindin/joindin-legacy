<?php
/**
 * Blog categories model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Blog categories model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Blog_cats_model extends Model
{
    /**
     * Returns categories from blog_cats table
     *
     * @return mixed
     */
    public function getCategories()
    {
        $q = $this->db->get('blog_cats');

        return $q->result();
    }
}

