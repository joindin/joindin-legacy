<?php
/**
 * Provides a method to avoid CSRF attacks
 *
 * @category Security
 * @package  Hooks
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * CSRF Protection Class
 * From http://net.tutsplus.com/tutorials/php/
 *      protect-a-codeigniter-application-against-csrf/
 * (with tweaks)
 *
 * @category Security
 * @package  Hooks
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class CSRF_Protection
{
    /**
     * @var CI instance
     */
    protected  $CI;

    /**
     * Name used to store token on session - also name of hidden field on form
     *
     * @var string
     */
    protected static $token_name = 'hash';

    /**
     * Stores a list of tokens 
     * @var array
     */
    protected static $tokens = array();

    // --------------------------------------------------------------------

    /**
     * Creates the CSRF_Protection instance and associates the CI instance
     * with it.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Generates a CSRF token and adds it to the list in the session.
     * Only the most recent five tokens are stored.
     * This must be tied to a post-controller hook, and before the hook
     * that calls the inject_tokens method().
     *
     * @return void
     */
    public function generate_token()
    {
        // Load session library if not loaded
        $this->CI->load->library('session');

        // Extract the list of tokens we currently know about
        self::$tokens = $this->CI->session->userdata(self::$token_name);
        if (!is_array(self::$tokens)) {
            self::$tokens = array();
        }

        // We only want to keep the most recent tokens
        if (count(self::$tokens) > 5) {
            array_pop(self::$tokens);
        }

        // Generate a new token for this request, add to the list
        $token = md5(uniqid() . microtime() . rand());
        array_unshift(self::$tokens, $token);

        // Store to the session
        $this->CI->session->set_userdata(self::$token_name, self::$tokens);
    }

    /**
     * Validates a submitted token when POST request is made.
     *
     * @return void
     */
    public function validate_tokens()
    {
        // Is this a post request?
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // is this an API request?
            if ( strpos($_SERVER['PATH_INFO'], '/api') === 0) {
                    // API call - don't expect a CSRF token
                    return;
            }

            // Is the token field set and valid?
            $posted_token = $this->CI->input->post(self::$token_name);
            $token_found  = in_array(
                $posted_token,
                $this->CI->session->userdata(self::$token_name)
            );

            if ($posted_token === false || !$token_found) {
                // Invalid request, send error 400.
                show_error('Request was invalid. Tokens did not match.', 400);
            }
        }
    }

    /**
     * This injects hidden tags on all POST forms with the csrf token.
     *
     * @return void
     */
    public function inject_tokens()
    { 
        $output = $this->CI->output->get_output();

        // Inject into any forms that use POST
        $output = preg_replace(
            '/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
            '$0<input type="hidden" name="' . self::$token_name 
            . '" value="' . self::$tokens[0] . '">',
            $output
        );

        $this->CI->output->_display($output);
    }

}
