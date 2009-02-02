<?php

function rating_image($rating)
{
    $rating = min(5, ceil((float)$rating));
	return  '<img class="rating rating-' . $rating . '" src="/inc/img/rating-' . $rating . '.gif" alt="Rating: ' . $rating . ' of 5"/>';
}

function rating_form($name, $selector = null, $required = true)
{
    if (null === $selector) {
        $selector = ':radio[name=' . $name . ']';
    }
    
    $str = '';
    
    for ($i = 1; $i <= 5; $i++) {
        $str .= '<input name="' . $name . '" value="' . $i . '" type="radio" class="star"/>';
    }
    
    $str .= '<script type="text/javascript">$(function(){ $("' . $selector . '").rating({required: ' . ($required ? 'true' : 'false') . '}); });</script>';
    
    return $str;
}

?>