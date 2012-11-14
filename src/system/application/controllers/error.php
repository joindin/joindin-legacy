<?php
/**
 * Error pages controller.
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
 * Error pages controller.
 *
 * Responsible for displaying the error pages.
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
class Error extends Controller
{

    /**
     * Displays the 404 page.
     *
     * @return void
     */
    function error_404()
    {
        $arr = array(
            'msg' => "404 - File not found"
        );
        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->write_view('content', 'error/404', $arr);
        $this->template->render();
    }
}

