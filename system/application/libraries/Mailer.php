<?php
/**
 * Class Mailer
 * @package Core
 * @subpackage Library
 */

/**
 * Simple class to send mails (just for convenience).
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Mailer
{

    /**
     * The headers for the mail
     * @var array
     */    
    protected $_headers = array();

    /**
     * The TO address
     * @var string
     */
    protected $_to = '';
        
    /**
     * The subject for the mail.
     * @var string
     */
    protected $_subject = '';
    
    /**
     * The body for the mail.
     * @param string
     */
    protected $_body = '';

    /** **/
    
    public function __construct($options)
    {
        if(!empty($options) && is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    /**
     * Sets options for Mailer all at once. Checks for set methods per option key.
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach($options as $option => $value) {
            $method = 'set' . ucfirst($option);
            if(method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
    /**
     * Sets the recipient address.
     * @param string $to
     */
    public function setTo($to)
    {
        $this->_to = $to;
    }
    
    /**
     * Set from header.
     * @param string $from
     */
    public function setFrom($from)
    {
        if(strpos(strtolower($from), 'from:') === 0) {
            $from = str_replace('from:', '', strtolower($from));
        }
        
        $this->setHeader('from', trim($from));
    }
    
    /**
     * Sets a header for the mail
     * @param string $key
     * @param string $value
     */
    public function setHeader($key, $value)
    {
        $this->_headers[strtolower($key)] = $value;
    }

    /**
     * Sets the subject for the mail.
     * @param string $subject
     */    
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }
    
    /**
     * Sets the body for the email
     * @param string $body
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }
    
    /**
     * Sets the email body by parsing a file through CI_View. It uses sprintf 
     * to replace variables that are handed down as parameters to the function.
     * @param string $filePath
     */
    public function setBodyFromFile($filePath)
    {
        // Slice of the first argument ($file)
        $replaceVars = array_slice(func_get_args(), 1);
        
        $ci_base = CI_Base::get_instance();
        $view = $ci_base->load->view($filePath, null, true);

        if(empty($view)) {
            throw new Exception('View file not loaded properly.');
        }
        
        // Replace the variables in the view with our replacements and store the body
        $this->_body = vsprintf($view, $replaceVars);
    }
    
    /**
     * Sends the mail
     * @return boolean
     */
    public function send()
    {
        $headers = '';
        foreach($this->_headers as $key => $value) {
            $headers .= ucfirst($key) . ': ' . $value . "\r\n";
        }
        
        @mail($this->_to, $this->_subject, $this->_body, $headers);
        
        return true;
    }

}
