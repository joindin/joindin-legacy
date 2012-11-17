<?php
/**
 * Helper for ratings
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
 * Get the correct image for the rating
 *
 * @param integer $rating The rating of the object
 * @param string  $scale  Whether to draw the thumbs "small" or "normal"
 *                        (default: normal)
 * 
 * @return string The correct rating image
 */
function rating_image($rating, $scale="normal")
{
    if ($scale == "small") {
        $width  = 80;
        $height = 14;
    } else {
        $width  = 117;
        $height = 21;
    }
    $rating = min(5, ceil((float)$rating));
    return  '<img class="rating rating-' . $rating . '" src="/inc/img/rating-' .
        $rating . '.gif" alt="Rating: ' . $rating . ' of 5" height="' . $height 
        . '" width="' . $width . '" />';
}

/**
 * Show the rating form
 *
 * @param string  $name     The name of the form
 * @param string  $value    The default/current value of the form
 * @param string  $selector The selector ID
 * @param boolean $required Indicates of the form is required
 * 
 * @return string The rating form
 */
function rating_form($name, $value = null, $selector = null, $required = true)
{
    if (null === $selector) {
        $selector = ':radio[name=' . $name . ']';
    }
    
    $str = '';
    
    for ($i = 1; $i <= 5; $i++) {
        $str .= '<input name="' . $name . '" value="' . $i .
                        '" type="radio" class="star"';
        if ((int)$value === $i) {
            $str .= 'checked="checked"';
        }
        $str .='/>';
    }
    
    $str .= '<script type="text/javascript">$(function() {
             $("' . $selector . '").rating(
                {required: ' . ($required ? 'true' : 'false') . '}); }
             );</script>';
    
    return $str;
}

