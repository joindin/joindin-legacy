<?php
/**
 * CSRF Protection Class
 * From http://net.tutsplus.com/tutorials/php/protect-a-codeigniter-application-against-csrf/
 * (with tweaks)
 */
class CSRF_Protection
{
    /**
     * @var CI instance
     */
    private $CI;

    /**
     * Name used to store token on session - also name of hidden field on form
     *
     * @var string
     */
    private static $token_name = 'hash';

    /**
     * Stores the token 
     * @var string
     */
    private static $token;

    // -----------------------------------------------------------------------------------

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Generates a CSRF token and stores it on session. Only one token per session is generated.
     * This must be tied to a post-controller hook, and before the hook
     * that calls the inject_tokens method().
     *
     * @return void
     */
    public function generate_token()
    {
        // Load session library if not loaded
        $this->CI->load->library('session');

        if ($this->CI->session->userdata(self::$token_name) === FALSE)
        {
            // Generate a token and store it on session, since old one appears to have expired.
            self::$token = md5(uniqid() . microtime() . rand());

            $this->CI->session->set_userdata(self::$token_name, self::$token);
        }
        else
        {
            // Set it to local variable for easy access
            self::$token = $this->CI->session->userdata(self::$token_name);
        }
    }

    /**
     * Validates a submitted token when POST request is made.
     *
     * @return void
     */
    public function validate_tokens()
    {
        // Is this a post request?
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {

            // is this an API request?
            if ( strpos($_SERVER['PATH_INFO'], '/api') === 0
                && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
                && $this->checkPublicKey()) {
                    // API call
                    $this->CI->session->set_userdata(self::$token_name, FALSE);
                    return;
            }

            // Is the token field set and valid?
            $posted_token = $this->CI->input->post(self::$token_name);
            if ($posted_token === FALSE || $posted_token != $this->CI->session->userdata(self::$token_name))
            {
                // Invalid request, send error 400.
                show_error('Request was invalid. Tokens did not match.', 400);
            }
        }
        // clear token, so that we get a new one for the next request
        $this->CI->session->set_userdata(self::$token_name, FALSE);
    }

    /**
     * Check an API request's public key is valid
     *
     * @return boolean
     */
    protected function checkPublicKey()
    {
        $this->CI->load->library('wsactions/BaseWsRequest');
        $baseWsRequest = new BaseWsRequest();
        return $baseWsRequest->checkPublicKey();
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
        $output = preg_replace('/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
                               '$0<input type="hidden" name="' . self::$token_name . '" value="' . self::$token . '">',
                               $output);

        $this->CI->output->_display($output);
    }

}
