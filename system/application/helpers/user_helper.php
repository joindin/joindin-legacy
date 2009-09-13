<?php

/**
 * Returns the authenticated user as an UserModel.
 * @return UserModel|null
 */
function user_get_model()
{
    if(user_is_authenticated()) {
        $ci_base = CI_Base::get_instance();
        $ci_base->load->model('UserModel');
        $userModel = $ci_base->UserModel->find(user_get_id());
        return $userModel;
    }
    else {
        return null;
    }
}

/**
 * Checks if the client is an authenticated user.
 */
function user_is_authenticated()
{
    $ci_base = CI_Base::get_instance();
	return (trim($ci_base->session->userdata('id')) != '' && is_numeric($ci_base->session->userdata('id')));
}

/**
 * Returns the username for the currently logged in user
 */
function user_get_username()
{
    $ci_base = CI_Base::get_instance();
	if (user_is_authenticated()) {
	    return $ci_base->session->userdata('username');
	}

	return null;
}

/**
 * Returns the display name for the currently logged in user
 */
function user_get_displayname()
{
    $ci_base = CI_Base::get_instance();
	if (user_is_authenticated()) {
	    return $ci_base->session->userdata('display_name');
	}

	return null;
}

/**
 * Returns the id for the currently logged in user.
 */
function user_get_id()
{
	$ci_base = CI_Base::get_instance();

	if (user_is_authenticated()) {
	    return $ci_base->session->userdata('id');
	}

	return null;
}

/**
 * Checks if the currently authenticated user has administative privileges.
 * @return boolean
 */
function user_is_administrator()
{
	if(user_is_authenticated()) {
	    $user = user_get_model();
	    return $user->isAdmin();
	}
	
	return false;
}

/**
 * Maintained for backwards compatibility.
 * @return boolean
 * @deprecated
 */
function user_is_admin()
{
    return user_is_administrator();
}

function user_is_admin_event($eventId)
{
	$CI =& get_instance();
	return (bool)$CI->user_model->isAdminEvent($eventId);
}

/**
 * Prints the name of the comment author. If the author is a registered user a 
 * link to the users profile will be printed.
 */
function print_comment_author($comment)
{
    if($comment->getUserId() != '' && $comment->getUser() instanceof UserModel) {
        echo '<a href="/user/view/' . $comment->getUser->getId() . '">' . escape($comment->getUser()->getUsername()) . '</a>';
    } else {
        echo $comment->getAuthorName();
    }
}

?>
