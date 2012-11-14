<?php
/**
 * Menu helper.
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
 * Function accepts arguments and builds up an array based on how many 
 * arguments are passed in. Each time the function is called it will 
 * return all the previous array elements along with a new array element
 * which will be built based on the following logic:
 *
 * Number of arguments:
 * 3 Args: First argument is title, second is content, third is included
 *      in the array but will be a numbered index
 * 2 Args: First is title, second is content
 * 1 Arg or more than 3: First argument is content, all other parts are
 *      ignored.
 *
 * @todo This method needs to be refactored as it requires more in explanation
 * to understand what it does than there is code that does it.
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
 * Function looks for an argument which will be appended to an array which
 * is returned.
 *
 * @return array
 */
function menu_pagetitle()
{
    static $title = array();

    if (func_num_args() > 0) {
        $title[] = func_get_arg(0);
    }

    return $title;
}

