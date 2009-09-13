<?php
/**
 * Class Blog
 * @package Core
 * @subpackage Controllers
 */

/**
 * Handles information regarding the joind.in blog.
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Blog extends Controller {
	
	function Blog(){
		parent::Controller();
	}
	
	/**
	 * Displays a list of blog posts.
	 */
	function index(){
		$this->load->model('BlogPostModel');
		
		$viewVars = array(
			'posts'	=> $this->BlogPostModel->findAll(null, '`date` DESC'),
		);
		
		$this->template->write('feedurl','/feed/blog');
		$this->template->write_view('content', 'blog/main', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Displays the details for a blog post
	 * @param int $id
	 */
	function view($id){
		$this->load->helper('form');
		$this->load->helper('reqkey');
		$this->load->helper('user');
		$this->load->model('BlogPostModel');
		$this->load->model('BlogCommentModel');
		
		// Find the model
		$post = $this->BlogPostModel->find($id);
		
		if(null === $post) {
		    redirect('blog');
		}
				
		// Update the number of views
		$post->incrementViews();
		
		$requestKey = buildReqKey();
		$viewVars = array (
		    'post' => $post,
		    'requestKey' => $requestKey,
		    'secretKey' => buildSecFile($requestKey)
		);
		
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		    $comment = new BlogCommentModel($_POST);
	        // Set some default values
	        $comment->setBlogPostId($post->getId());
	        $comment->setDate(time());
	        
	        // Check for authenticated user
	        if(user_is_authenticated()) {
	            $comment->setUserId(user_get_id());
	            $comment->setAuthorName(user_get_displayname());
	        }
	        
	        if($comment->validate()) {
	            // Check if the message was spam
	            
	            $comment->save();
	            
	            /** Mailer **/
	            require_once BASEPATH . 'application/libraries/Mailer.php';
	            
	            $mail = new Mailer(array (
	                'to' => $this->config->item('mail_admin'),
	                'from' => $this->config->item('mail_feedback'),
	                'subject' => 'joind.in: Blog comment on entry ' . $post->getId()
	            ));
	            $mail->setBodyFromFile(
	                'mail/blog_comment',
	                date('M j, Y H:i', $comment->getDate()),
	                $post->getTitle(),
	                $comment->getAuthorName(),
	                escape($comment->getComment())
	            );
	            $mail->send();
	            
	            // Redirect back to the blog post
	            redirect('blog/view/' . $post->getId() . '#comments', 'location', 302);
	        }
	        else {
	            if(!$comment->isSpam()) {
    	            $viewVars['comment'] = $comment;
	                $viewVars['error'] = $comment->getErrors();
	            }
	        }
		    
		}
		
		// Fetch data for the blog sidebar
		$postsSidebarData = array (
		    'title' => 'Popular Blog Posts',
		    'posts' => $this->BlogPostModel->findAll("`id` != '{$post->getId()}'", '`date` DESC', 10)
		);
		
		$this->template->write('feedurl', '/feed/blog');
		$this->template->write_view('sidebar2', 'blog/_other-posts', $postsSidebarData);
		$this->template->write_view('content', 'blog/view', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Adds a new blog post to the site
	 */
    function add()
	{
	    $this->_showForm();
	}
	
	/**
	 * Displays the blog post form with the values in it.
	 * @param int $id
	 */
	function edit($id){
		$this->_showForm($id);
	}
	
	/**
	 * Deletes a blog post and all of it's comments
	 * @param int $id
	 */
	function delete($id)
	{
        $this->load->model('BlogPostModel');
        
        $post = $this->BlogPostModel->find($id);
        
        if(null === $post) {
            show_404('/blog/delete/' . $id);
        }
        
        $success = $post->delete();
        if($success) {
            $this->session->set_flashdata('message', 'Blog post deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Blog post was not deleted!');
        }
        
        redirect('/blog');
	}
	
	/**
	 * Shows the blog form.
	 * @param int $id
	 */
	function _showForm($id = null)
	{
	    $this->load->helper('user');
	    
		if(!user_is_administrator()) { 
		    redirect(); 
		}
		
		$this->load->model('BlogPostModel');
		$viewVars = array();
		
		if(null !== $id && is_numeric($id)) {
		    
		    // Try to find the blog post
		    $post = $this->BlogPostModel->find($id);
		    
		    if(null === $post) {
		        show_404('/blog/edit/' . $id);
		    }
		    
		    $viewVars = array (
		        'title' => 'Edit Blog Post',
		        'action' => '/blog/edit/' . $post->getId(),
		        'post' => $post
		    );
		}
		else {
		    $post = new BlogPostModel();
		    $post->setUserId(user_get_id());
		    $viewVars = array(
		        'title' => 'Add Blog Post',
		        'action' => '/blog/add',
		        'post' => $post
		    );
		}
		
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
		    $post->setData($_POST);
		    // reconstruct the date
		    $date = mktime(
		        $_POST['post_hour'],
		        $_POST['post_minute'],
		        0,
		        $_POST['post_month'],
		        $_POST['post_day'],
		        $_POST['post_year']
		    );
		    $post->setDate($date);

		    if($post->validate()) {
		        $post->save();
		        $viewVars['message']= 'Post saved successfully! <a href="/blog/view/' . $post->getId() . '">View post</a>';
		    }
		    else {
		        $viewVars['error'] = $post->getErrors();
		    }
		}
		
		$this->template->write_view('content', 'blog/form', $viewVars);
		$this->template->render();
	}
}

