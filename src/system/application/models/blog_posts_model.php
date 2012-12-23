<?php
/**
 * Blog posts model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Blog posts model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Blog_posts_model extends Model
{
    /**
     * Retrieves post details
     *
     * @param integer $id Post id
     *
     * @return mixed
     */
    public function getPostDetail($id = null)
    {
        $w   = ($id) ? 'where ID=' . $this->db->escape($id) : '';
        $sql = sprintf(
            '
            select
                bp.title,
                bp.content,
                bp.date_posted,
                bp.author_id,
                bp.ID,
                (select count(ID) from blog_comments
                where blog_post_id=bp.ID) comment_count
            from
                blog_posts bp
            %s
            order by
                bp.date_posted desc
        ', $w
        );

        $q = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Increments the post view counter
     *
     * @param integer $id Post id
     *
     * @return void
     */
    public function updatePostViews($id)
    {
        $sql = 'update blog_posts set views=views+1';
        $this->db->query($sql);
    }

    /**
     * Retrieves the newest blog post
     *
     * @return mixed
     */
    public function getLatestPost()
    {
        $this->db->from('blog_posts');
        $this->db->order_by('date_posted', 'desc');
        $this->db->limit(1);
        $q = $this->db->get();

        return $q->result();
    }
}

