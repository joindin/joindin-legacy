<?php
/**
 * Menu helper.
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
 * Sets and returns the current area.
 *
 * @return string
 */
function menu_get_current_area()
{
    return menu_set_current_area();
}

/**
 * Sets and returns the current area.
 *
 * @return string
 */
function menu_set_current_area()
{
    static $currentArea;

    if (func_num_args() > 0) {
        $currentArea = func_get_arg(0);
    }

    if ($currentArea === null) {
        $CI =& get_instance();
        if (isset($CI->uri->segments[1])) {
            $currentArea = $CI->uri->segments[1];
        } else {
            $currentArea = 'home';
        }
    }

    return $currentArea;
}

/**
 * Collects the data necessary for the sidebar and returns it in the
 * required format.
 *
 * @param string [title]   Title
 * @param string [content] Content
 * @param string [...]     Items to display
 *
 * @return array
 */
function menu_sidebar()
{
    static $sidebar = array();

    $numArgs = func_num_args();
    if ($numArgs > 0) {
        switch ($numArgs) {
        case 3:
            $sidebar[] = array(
                'title'   => func_get_arg(0),
                'content' => func_get_arg(1)
            ) + func_get_arg(2);
            break;
        case 2:
            $sidebar[] = array(
                'title'   => func_get_arg(0),
                'content' => func_get_arg(1)
            );
            break;
        case 1:
        default:
            $sidebar[] = array(
                'title'   => null,
                'content' => func_get_arg(0)
            );
            break;
        }
    }

    return $sidebar;
}

/**
 * Sets and gets the title of the page.
 *
 * @param [title] Title of this page.
 *
 * @return string
 */
function menu_pagetitle()
{
    static $title = array();

    if (func_num_args() > 0) {
        $title[] = func_get_arg(0);
    }

    return $title;
}

?>