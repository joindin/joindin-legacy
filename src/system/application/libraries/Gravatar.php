<?php
/**
 * Joind.in Gravatar
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Joind.in Gravatar
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */
class Gravatar
{
    
    private $_servicePath    = 'http://www.gravatar.com/avatar';
    private $_servicePathSSL = 'https://secure.gravatar.com/avatar';
    private $CI              = null;

    /**
     * Creates the Gravatar object
     */
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * Return Gravatar path for user
     *
     * @param integer $userId    User ID to get email from (if email not given)
     * @param string  $userEmail Email address to use for Gravatar
     * @param int     $size      Desired size
     *
     * @return string
     */
    public function displayUserImage($userId, $userEmail=null, $size=null)
    {
        if ($userId === false) {
            return false;
        }

        // Get the user's email address
        $this->CI->load->model('user_model');
        if ($userEmail === null) {
            $userDetail = $this->CI->user_model->getUserById($userId);
            if (empty($userDetail)) {
                return false;
            }
            $userEmail = $userDetail[0]->email;
        }

        // Build the Gravatar URL
        $hash = $this->buildEmailHash($userEmail);
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $path = $this->_servicePathSSL . '/' . $hash . '?d=mm';
        } else {
            $path = $this->_servicePath . '/' . $hash . '?d=mm';
        }
        if ($size !== null && is_int($size)) {
            $path .= '&s=' . urlencode($size);
        }
        
        return $path;
    }
    
    /**
     * Build has of user's email for the Gravatar request
     *
     * @param string $userEmail User email address
     *
     * @return string
     */
    protected function buildEmailHash($userEmail)
    {
        $userEmail = strtolower(trim($userEmail));
        return md5($userEmail);
    }

    /**
     * Decorates users with the gravatar
     *
     * @param array &$users Array of users
     * @param int   $size   Size for gravatar
     *
     * @return bool
     */
    public function decorateUsers(&$users, $size=null)
    {
        foreach ($users as $id=>$user) {
            if (isset($user->ID)) {
                $users[$id]->gravatar = $this->displayUserImage(
                    $user->ID,
                    (isset($user->email)?$user->email:null),
                    $size
                );
            } else if (isset($user->id)) {
                $users[$id]->gravatar = $this->displayUserImage(
                    $user->id,
                    (isset($user->email)?$user->email:null),
                    $size
                );
            }
        }
        return true; // Modifies array in-place, therefore no real return
    }
}

