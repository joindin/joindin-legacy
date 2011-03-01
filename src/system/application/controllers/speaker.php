<?php
/**
 * Speaker pages controller.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @author    Chris Cornutt <chris@joind.in>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * Speaker pages controller.
 *
 * Responsible for displaying the search page and results.
 *
 * @category  Joind.in
 * @package   Controllers
 * @author    Chris Cornutt <chris@joind.in>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
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
class Speaker extends CI_Controller
{
    /**
     * Displays the profile page for a speaker.
     *
     * @return void
     */
    public function profile()
    {
        $this->load->model('speaker_profile_model', 'sp');

        $udata = $this->user_model->getUser($this->session->userdata('ID'));
        $arr   = array(
            'pdata' => $this->sp->getProfile($udata[0]->ID)
        );

        if (!empty($arr['pdata'][0]->picture)) {
            $picture     = strtolower($arr['pdata'][0]->picture);
            $p = $this->config->item('user_pic_path') . '/' . $picture;

            $profile_pic = (is_file($p))
                ? '/inc/img/profile/' .$picture
                : null;

            $arr['pdata'][0]->profile_pic = $profile_pic;
        }

        $this->template->write_view('content', 'speaker/profile', $arr);
        $this->template->render();
    }

    /**
     * Create/modify the information in a speaker's profile.
     *
     * @return void
     */
    public function edit()
    {
        // if the user is not logged in, redirect to the login page
        if (!$this->user_model->isAuth()) {
            redirect('user/login');
        }

        $pic_err    = null;
        $resume_err = null;

        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->model('speaker_profile_model', 'sp');
        $this->load->model('countries_model', 'co');

        $udata = $this->user_model->getUser($this->session->userdata('ID'));

        $fields = array(
            'full_name'  => 'Full Name',
            'email'      => 'Email',
            'website'    => 'Website',
            'blog'       => 'Blog',
            'phone'      => 'Phone',
            'job_title'  => 'Job Title',
            'bio'        => 'Bio',
            'street'     => 'Street',
            'city'       => 'City',
            'zip'        => 'Zip',
            'country_id' => 'Country',
            'resume'     => 'Resume',
            'picture'    => 'Picture'
        );
        $rules = array(
            'full_name' => 'required',
            'email'     => 'required|valid_email',
            'bio'       => 'required'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        // if we have profile settings, assign them
        $cdata = $this->sp->getProfile($udata[0]->ID);
        if (isset($cdata[0])) {
            foreach ($cdata[0] as $k => $v) {
                $this->validation->$k = $v;
            }
        }

        // run the form!
        if ($this->validation->run() != false) {
            // set up the upload for the image
            $config = array(
                'upload_path'   => $this->config->item('user_pic_path'),
                'allowed_types' => 'jpg|gif|png', 'overwrite' => true,
                'max_size'      => 2000,
            );
            $this->load->library('upload', $config);

            // Check for picture upload...reset our filename if it's there
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
                $ext = strrchr($_FILES['picture']['name'], '.');

                $_FILES['picture']['name'] = 'user_pic_' . $udata[0]->ID . $ext;
            }

            // check for resume upload...reset out filename if it's there
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
                $ext = strrchr($_FILES['resume']['name'], '.');

                $_FILES['resume']['name'] = 'user_resume_' . $udata[0]->ID . $ext;
            }

            // only run the upload if they've given us an image
            $up_data = array();
            $up_err  = '';
            if (isset($_FILES['picture']['name'])
                && ($_FILES['picture']['name'] != '')
            ) {
                $this->upload->do_upload('picture');

                $up_err  = $this->upload->display_errors('', '');
                $up_data = $this->upload->data();
            }

            $data = array(
                'user_id'       => $udata[0]->ID,
                'country_id'    => $this->input->post('country_id'),
                'full_name'     => $this->input->post('full_name'),
                'contact_email' => $this->input->post('email'),
                'website'       => $this->input->post('website'),
                'blog'          => $this->input->post('blog'),
                'phone'         => $this->input->post('phone'),
                'city'          => $this->input->post('city'),
                'zip'           => $this->input->post('zip'),
                'street'        => $this->input->post('street'),
                'job_title'     => $this->input->post('job_title'),
                'bio'           => $this->input->post('bio')
            );

            if (isset($up_data['file_name'])) {
                $data['picture'] = $up_data['file_name'];
            }

            if ($up_err) {
                $this->validation->error_string = $up_err;
            } elseif (isset($cdata[0])) {
                $this->sp->updateProfile($udata[0]->ID, $data);
                $this->validation->error_string = 'Profile successfully updated!';
            } else {
                $this->sp->setProfile($data);
                $this->validation->error_string = 'Profile successfully saved!';
            }

            $this->validation->error_string .= '<br/>Please ' .
                '<a href="/speaker/profile">click here</a> ' .
                'to return to your speaker profile';
        } else {
            // If there's not an data set, get from their profile
            if (empty($this->validation->email)) {
                $udata = $this->user_model->getUser(
                    $this->session->userdata('ID')
                );
                $this->validation->email     = $udata[0]->email;
                $this->validation->full_name = $udata[0]->full_name;
            }
        }

        $msg  = $this->validation->error_string;
        $msg .= ($pic_err) ? 'Profile Image: ' . $pic_err : '';
        $msg .= ($resume_err) ? 'Resume Upload: ' . $resume_err : '';

        $profile_pic = null;
        if (!empty($cdata[0]->picture)) {
            $p = $this->config->item('user_data') . '/' . $cdata[0]->picture;

            if (is_file($p)) {
                $profile_pic = '/inc/img/profile/' . $cdata[0]->picture;
            }
        }

        $countries = array();
        foreach ($this->co->getCountries() as $row) {
            $countries[$row->ID] = $row->name;
        }

        $arr = array(
            'msg'       => $msg, 'profile_pic' => $profile_pic,
            'countries' => $countries
        );

        $this->template->write_view('content', 'speaker/edit', $arr);
        $this->template->render();
    }

    /**
     * Define the access levels for different versions of the speaker's profile.
     *
     * @return void
     */
    public function access()
    {
        // if the user is not logged in, redirect to the login page
        if (!$this->user_model->isAuth()) {
            redirect('user/login');
        }

        $this->load->model('speaker_profile_model', 'spm');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('validation');

        $p        = explode('/', uri_string());
        $arr      = array();
        $req_type = null;
        $view     = '';

        if (isset($p[3])) {
            $req_type  = strtolower($p[3]);
            $is_public = null;

            switch ($req_type) {
            case 'add':
            case 'edit':
                // adding a new token and corresponding access
                $view  = 'speaker/access_add';
                $rules = array(
                    'fields'     => 'required',
                    'token_name' => 'required|alpha_numeric|' .
                        'callback_token_name_check',
                    'token_desc' => 'required'
                );
                $fields = array(
                    'fields'     => 'Items',
                    'token_name' => 'Token Name',
                    'token_desc' => 'Token Description',
                    'is_public'  => 'Publicly Viewable'
                );
                $arr['curr_access'] = array();
                $this->validation->set_rules($rules);
                $this->validation->set_fields($fields);

                // if we're editing, get the profile's details
                if ($req_type == 'edit') {
                    $profile = $this->spm->getProfile(
                        $this->session->userdata('ID')
                    );
                    $tokens  = $this->spm->getProfileTokens($profile[0]->ID);

                    // be sure the one they're trying to edit is theirs....
                    $pass  = false;
                    $found = null;
                    foreach ($tokens as $t) {
                        if ($t->ID == $p[4]) {
                            $pass  = true;
                            $found = $t;
                        }
                    }
                    if (!$pass) {
                        redirect('speaker/access');
                    }

                    $token = $found;
                    $arr['curr_access'] = $this->spm->getTokenAccess($p[4]);
                    $arr['token_id']    = $token->ID;

                    $this->validation->token_name = $token->access_token;
                    $this->validation->token_desc = $token->description;
                    $this->validation->is_public  = $token->is_public;
                }

                if ($this->validation->run() != false) {
                    $fields = $this->input->post('fields');
                    if (count($fields) <= 0) {
                        $arr['msg'] = 'You must select at least one '.
                            'field to include in this access!';
                    } else {
                        // okay, so - we're good... we have a name, a
                        // description and the fields to add, let's build
                        // this thing

                        // are we editing?
                        if ($req_type == 'edit') {
                            $is_public
                                = ($this->input->post('is_public') === '1')
                                    ? 'Y' : null;

                            $uid    = $this->session->userdata('ID');
                            $tid    = $this->input->post('token_id');
                            $status = $this->spm->updateProfileAccess(
                                $uid, $tid, $fields, $is_public
                            );

                            $arr['msg'] = ($status)
                                ? 'Profile Access Updated!'
                                : 'There has been an error!';

                            // we want the latest values out there...
                            $arr['curr_access'] = $fields;
                        } else {
                            $is_public
                                = ($this->input->post('is_public')==='1')
                                    ? 'Y' : null;

                            $uid    = $this->session->userdata('ID');
                            $name   = $this->input->post('token_name');
                            $desc   = $this->input->post('token_desc');
                            $status = $this->spm->setProfileAccess(
                                $uid, $name, $desc, $fields, $is_public
                            );

                            $arr['msg'] = ($status)
                                ? 'Profile Access Added!'
                                : 'There has been an error!';
                        }
                    }
                } else {
                    $this->validation->set_message(
                        'fields', 'You must select at least one field!'
                    );
                    $arr['msg'] = $this->validation->error_string;
                }
                break;
            case 'delete':
                // delete a current token and fields
                $arr['tid'] = $p[4];
                $sub        = $this->input->post('sub');
                if ($sub) {
                    if ($sub == 'No') {
                        redirect('speaker/access');
                    } else {
                        $uid = $this->session->userdata('ID');
                        $this->spm->deleteProfileAccess($uid, $p[4]);
                        redirect('speaker/access');
                    }
                }

                $view = 'speaker/access_del';
                break;
            default:
                $view = 'speaker/access';
                break;
            }
        } else {
            $view = 'speaker/access';
        }

        $udata = $this->user_model->getUser($this->session->userdata('ID'));

        $arr['req_type']    = $req_type;
        $arr['access_data'] = $this->spm->getUserProfileAccess($udata[0]->ID);

        $this->template->write_view('content', $view, $arr);
        $this->template->render();
    }

    /**
     * Callback for the token validator.
     *
     * @param string $val Value provided by the form validator
     *
     * @return bool
     */
    function token_name_check($val)
    {
        // if we're editing, this doesn't matter
        $p = explode('/', uri_string());
        if (isset($p[3]) && strtolower($p[3]) == 'edit') {
            return true;
        }

        $this->load->model('speaker_profile_model', 'spm');

        $ret = $this->spm->getTokenDetail($val);
        if (!empty($ret)) {
            $this->validation->set_message(
                'token_name_check', 'Invalid! Token name must be unique!'
            );

            return false;
        }

        return true;
    }
}