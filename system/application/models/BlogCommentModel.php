<?php
/**
 * Class BlogCommentModel
 * @package Core
 * @subpackage Models
 */

/** CommentModel */
require_once BASEPATH . 'application/models/CommentModel.php';
/** BlogPostModel */
require_once BASEPATH . 'application/models/BlogPostModel.php';
/** UserModel */
require_once BASEPATH . 'application/models/UserModel.php';

/**
 * Reperesents a comment on a blog post.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class BlogCommentModel extends CommentModel
{
    
    /**
     * @see DomainModel::$_table
     */
    protected $_table = 'blog_comments';
    
    /**
     * @see DomainModel::$_belongsTo
     */
    protected $_belongsTo = array (
        'Post' => array (
            'className' => 'BlogPostModel',
            'referenceColumn' => 'blog_post_id',
            'foreignColumn' => 'id'
        ),
        'Author' => array (
            'className' => 'UserModel',
            'referenceColumn' => 'user_id',
            'foreignColumn' => 'id'
        )
    );
    
    /**
     * @see DomainModel::$_rules
     */
    protected $_rules = array (
        'blog_post_id' => array('required', 'numeric'),
        'author_name' => array('required', 'alpha', 'strip_tags'),
        'comment' => array('required', 'strip_tags')
    );

}

