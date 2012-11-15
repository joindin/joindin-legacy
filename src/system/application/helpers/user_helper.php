<?php
/**
 * Helper for views related to user
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
 * Check if the user is authorized
 * 
 * @return boolean Indicating wether or not the user is authorized
 */
function user_is_auth()
{
    $CI = &get_instance();
    return (bool)$CI->user_model->isAuth();
}

/**
 * Get the username of the current user
 *
 * @return string The username
 */
function user_get_username()
{
    $CI = &get_instance();

    if ($CI->user_model->isAuth()) {
        return $CI->session->userdata('username');
    }

    return null;
}

/**
 * Get the id of the current user
 *
 * @return integer The user id
 */
function user_get_id()
{
    $CI = &get_instance();

    if ($CI->user_model->isAuth()) {
        return $CI->session->userdata('ID');
    }

    return null;
}

/**
 * Return if the user is a administrator or not
 * 
 * @return boolean The user administrator state
 */
function user_is_admin()
{
    $CI = &get_instance();
    return (bool)$CI->user_model->isSiteAdmin();
}

/**
 * Check if the user is administrator of the event
 *
 * @param integer $eventId The id of the event to check
 * 
 * @return boolean The user is an administrator of this event
 */
function user_is_admin_event($eventId)
{
    $CI = &get_instance();
    return (bool)$CI->user_model->isAdminEvent($eventId);
}

