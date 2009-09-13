<?php
/**
 * Class BlogPostModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** UserModel */
require_once BASEPATH . 'application/models/UserModel.php';
/** BlogCommentModel */
require_once BASEPATH . 'application/models/BlogCommentModel.php';

/**
 * Represents a blog post
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class BlogPostModel extends DomainModel
{

    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'blog_posts';
    
    /**
     * @see DomainModel::$_belongsTo
     */
    protected $_belongsTo = array (
        'Author' => array (
            'className' => 'UserModel',
            'referenceColumn' => 'user_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * @see DomainModel::$_hasMany
     */
    protected $_hasMany = array (
        'Comments' => array (
            'className' => 'BlogCommentModel',
            'referenceColumn' => 'id',
            'foreignColumn' => 'blog_post_id',
            'cascadeOnDelete' => true
        )
    );
    
    /**
     * @see DomainModel::$_table
     */
    protected $_rules = array (
        'title' => array('required'),
        'content' => array('required'),
        'date' => array('required')
    );
    
    /** **/
    
    /**
     * Returns the number of comments for this blog post.
     * @return int
     */
    public function getCommentCount()
    {
        return count($this->getComments());
    }
    
    /**
     * Returns the latest blog post.
     * @return BlogPostModel
     */
    public function getLatestPost()
    {
        $post = $this->findAll(null, '`date` DESC', 1);
        $post = array_shift($post);
        
        return $post;
    }
    
    /**
     * Increments the number of views by one and saves the model;
     */
    public function incrementViews()
    {
        $this->_data['views']++;
        $this->save();
    }
    
    /**
     * Deletes all the comments related to this BlogPost.
     */
    protected function postDelete($success)
    {
        if($success) {
            foreach($this->getComments() as $comment) {
                $comment->delete();
            }
        }
    }

}
