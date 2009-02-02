<?php

function user_is_auth()
{
	$CI =& get_instance();
	return (bool)$CI->user_model->isAuth();
}

function user_get_username()
{
	$CI =& get_instance();
	return false !== ($username = $CI->user_model->isAuth()) ? $username : null;
}

function user_is_admin()
{
	$CI =& get_instance();
	return (bool)$CI->user_model->isSiteAdmin();
}

function user_is_admin_event($eventId)
{
	$CI =& get_instance();
	return (bool)$CI->user_model->isAdminEvent($eventId);
}

?>