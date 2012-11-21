<?php  
/**
 * Joindin config file
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

//
// CSRF Protection hooks, don't touch these unless you know what you're
// doing.
// See http://net.tutsplus.com/tutorials/php/
//      protect-a-codeigniter-application-against-csrf/
//
// THE ORDER OF THESE HOOKS IS EXTREMELY IMPORTANT!!
//

// THIS HAS TO GO FIRST IN THE post_controller_constructor HOOK LIST.
$hook['post_controller_constructor'][] = array( 
    // Mind the "[]", this is not the only post_controller_constructor hook
    'class'    => 'CSRF_Protection',
    'function' => 'validate_tokens',
    'filename' => 'csrf.php',
    'filepath' => 'hooks'
);

// Generates the token (MUST HAPPEN AFTER THE VALIDATION HAS BEEN MADE,
// BUT BEFORE THE CONTROLLER IS EXECUTED, OTHERWISE USER HAS NO ACCESS TO 
// A VALID TOKEN FOR CUSTOM FORMS).
$hook['post_controller_constructor'][] = array( 
    // Mind the "[]", this is not the only post_controller_constructor hook
    'class'    => 'CSRF_Protection',
    'function' => 'generate_token',
    'filename' => 'csrf.php',
    'filepath' => 'hooks'
);

// This injects tokens on all forms
$hook['display_override'] = array(
    'class'    => 'CSRF_Protection',
    'function' => 'inject_tokens',
    'filename' => 'csrf.php',
    'filepath' => 'hooks'
);



/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */
