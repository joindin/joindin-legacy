<?php
/**
 * Blog comments model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Blog comments model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Blog_comments_model extends Model
{

    /**
     * Retrieves comments by post id
     *
     * @param integer $postId Post id
     *
     * @return mixed
     */
    public function getCommentsByPostId($postId)
    {
        $query = $this->db->get_where(
            'blog_comments',
            array('blog_post_id' => $postId)
        );

        return $query->result();
    }
}

