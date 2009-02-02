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

    if (func_num_args() > 0) {
        $sidebar[] = array(
            'title'   => func_get_arg(0),
            'content' => func_get_arg(1)
        );
    }

	return $sidebar;
}

?>