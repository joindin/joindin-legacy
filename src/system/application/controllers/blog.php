<?php
/**
 * Blog pages controller.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * Blog pages controller.
 *
 * Responsible for displaying pages related to the blogging functionality of
 * Joind in.
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 *
 * @property  CI_Config   $config
 * @property  CI_Loader   $load
 * @property  CI_Template $template
 * @property  CI_Input    $input
 * @property  User_model  $user_model
 */
class Blog extends Controller
{

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function Blog()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * Displays a list all blog entries.
     *
     * @return void
     */
    function index()
    {
        $this->load->model('blog_posts_model', 'blogPostsModel');

        $arr = array(
            'posts'    => $this->blogPostsModel->getPostDetail(),
            'is_admin' => $this->user_model->isSiteAdmin()
        );

        $this->template->write('feedurl', '/feed/blog');
        $this->template->write_view('content', 'blog/main', $arr);
        $this->template->write_view('sidebar2', 'about/_facebook-sidebar');
        $this->template->render();
    }

    /**
     * Displays a entry form, inserts a new blog posting or if an ID is
     * provided, updates an existing one.
     *
     * @param integer $id ID of the blog post to update (optional)
     *
     * @return void
     */
    function add($id = null)
    {
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }

        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->model('blog_posts_model');
        $this->load->model('blog_cats_model');
        $this->load->model('blog_post_cat_model', 'bpcat');
        $arr = array();

        $fields = array(
            'title'    => 'Entry Title',
            'story'    => 'Entry Content',
            'post_mo'  => 'Post Month',
            'post_day' => 'Post Day',
            'post_yr'  => 'Post Year',
            'post_hr'  => 'Post Hour',
            'post_mi'  => 'Post Minute',
            'category' => 'Category'
        );
        $rules  = array(
            'title'    => 'required',
            'story'    => 'required',
            'post_mo'  => 'required',
            'post_day' => 'required',
            'post_yr'  => 'required',
            'post_hr'  => 'required',
            'post_mi'  => 'required',
            'category' => 'required'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if ($this->validation->run() != false) {
            $post_date = mktime(
                $this->input->post('post_hr'),
                $this->input->post('post_mi'),
                0,
                $this->input->post('post_mo'),
                $this->input->post('post_day'),
                $this->input->post('post_yr')
            );

            $arr = array(
                'title'       => $this->input->post('title'),
                'content'     => $this->input->post('story'),
                'date_posted' => $post_date,
                'author_id'   => ''
            );

            if ($id) {
                $this->db->where('ID', $id);
                $this->db->update('blog_posts', $arr);
            } else {
                $arr['views'] = 0;
                $this->db->insert('blog_posts', $arr);
                $id = $this->db->insert_id();
            }
            $this->bpcat->setPostCat($id, $this->input->post('category'));

            $arr = array('msg' => 'Post inserted successfully!
                <a href="/blog/view/' . $id . '">View post</a>');
        } else {
            if ($id) {
                $det = $this->blog_posts_model->getPostDetail($id); //print_r($det);

                $this->validation->title = $det[0]->title;
                $this->validation->story = $det[0]->content;

                $this->validation->post_mo   = date('m', $det[0]->date_posted);
                $this->validation->post_day  = date('d', $det[0]->date_posted);
                $this->validation->post_year = date('Y', $det[0]->date_posted);

                $this->validation->post_hr = date('H', $det[0]->date_posted);
                $this->validation->post_mi = date('i', $det[0]->date_posted);
            }
        }
        $arr['edit_id'] = ($id) ? $id : null;
        $arr['cats']    = $this->blog_cats_model->getCategories();

        $this->template->write_view('content', 'blog/add', $arr);
        $this->template->render();
    }

    /**
     * Displays the edit page for, or updates, a blog post.
     *
     * Redirects to the add method
     *
     * @param integer $id ID of the blog post to update
     *
     * @see Blog::add()
     *
     * @return void
     */
    function edit($id)
    {
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }
        $this->add($id);
    }

    /**
     * Deletes the selected blog post
     * 
     * @param integer $id ID of the blog post to delete
     * 
     * @return void
     */
    function delete($id)
    {
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }

        $this->load->helper('form');
        $this->load->library('validation');

        $arr = array(
            'bid' => $id,
        );

        $confirm = $this->input->post('confirm');

        if (isset($confirm) && ($confirm == 'yes')) {
            $this->db->delete('blog_posts', array('ID' => $id));
            $arr = array();
        } else if (isset($confirm) && ($confirm == 'no')) {
            // Used CI's redirect function to stop 2 copies of the same page showing
            redirect('/blog/view/' . $id, 'refresh');
        }

        $this->template->write_view('content', 'blog/delete', $arr, true);
        $this->template->render();
    }

    /**
     * Displays the details of a specific blog post.
     *
     * @param integer $id ID of the blog post to display
     *
     * @return void
     */
    function view($id)
    {
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->library('akismet');
        $this->load->library('defensio');
        $this->load->library('spam');
        $this->load->helper('reqkey');
        $this->load->model('blog_posts_model', 'blogPostsModel');
        $this->load->model('blog_comments_model', 'blogCommentsModel');

        $this->blogPostsModel->updatePostViews($id);
        $reqkey = buildReqKey();

        $arr        = array(
            'details'  => $this->blogPostsModel->getPostDetail($id),
            'is_admin' => $this->user_model->isSiteAdmin(),
            'reqkey'   => $reqkey, 'seckey' => buildSecFile($reqkey),
            'comments' => $this->blogCommentsModel->getCommentsByPostId($id)
        );
        $other_data = array(
            'title' => 'Popular Blog Posts',
            'posts' => $this->blogPostsModel->getPostDetail(), 'curr_id' => $id
        );

        if ($username = $this->user_model->isAuth()) {
            $udata = $this->user_model->getUserByUsername($username);

            $arr['full_name'] = (!empty($udata[0]->full_name))
                ? $udata[0]->full_name
                : $udata[0]->username;
        }
        $this->template->write('feedurl', '/feed/blog');
        $this->template->write_view(
            'sidebar2',
            'blog/_other-posts',
            $other_data
        );
        $this->template->write_view('content', 'blog/view', $arr);
        $this->template->render();
    }
}

