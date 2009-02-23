<?php

function menu_get_current_area()
{
	return menu_set_current_area();
}

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

function menu_sidebar()
{
    static $sidebar = array();

    $numArgs = func_num_args();
    if ($numArgs > 0) {
        switch ($numArgs) {
            case 3:
                $sidebar[] = array('title' => func_get_arg(0), 'content' => func_get_arg(1)) + func_get_arg(2);
                break;
            case 2:
                $sidebar[] = array('title' => func_get_arg(0), 'content' => func_get_arg(1));
                break;
            case 1:
            default:
                $sidebar[] = array('title' => null, 'content' => func_get_arg(0));
                break;
        }
    }

	return $sidebar;
}

function menu_pagetitle()
{
    static $title = array();

    if (func_num_args() > 0) {
        $title[] = func_get_arg(0);
    }

	return $title;
}

?>