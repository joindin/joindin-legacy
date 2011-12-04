<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
|==========================================================
| Create Captcha
|==========================================================
|
*/
function create_captcha()
{
    $seed = rand();
    return get_captcha($seed);
}

function captcha_get_digits($large_set = false) {
    // @TODO: This needs more translations
    $digits = array(
            0 => "zero",
            1 => "one",
            2 => "two",
            3 => "three",
            4 => "four",
            5 => "five",
            6 => "six",
            7 => "seven",
            8 => "eight",
            9 => "nine"
    );

    if ($large_set) {
        // Large set is needed to convert captha math sums to digits (ie: 8 + 4 == twelve)
        $digits = array_merge($digits, array(
            10 => "ten",
            11 => "eleven",
            12 => "twelve",
            13 => "thirteen",
            14 => "fourteen",
            15 => "fifteen",
            16 => "sixteen",
            17 => "seventeen",
            18 => "eighteen",
        ));
    }
    return $digits;
}

function get_captcha($seed)
{
    $digits = captcha_get_digits();

    // Initialize the RNG
    srand($seed);

    $k = array_rand($digits, 2);
    if (rand(0,1) == 1) {
        $value = $k[0] + $k[1];
        $text = $digits[$k[0]]." plus ".$digits[$k[1]];
    } else {
        // Swap when digit 1 is less than digit 2, this makes sure we don't get negative values
        if ($k[1] > $k[0]) {
            $tmp = $k[0];
            $k[0] = $k[1];
            $k[1] = $tmp;
        }
        $value = $k[0] - $k[1];
        $text = $digits[$k[0]]." minus ".$digits[$k[1]];
    }

    return array('seed' => $seed, 'value' => $value, 'text' => $text);
}
