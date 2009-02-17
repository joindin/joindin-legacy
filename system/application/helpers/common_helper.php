<?php

function escape($str)
{
	// Translates all non-unicode entities
    $str = preg_replace(
    	'/&(?!(#[0-9]+|[a-z0-9]+);)/si',
        '&amp;',
        $str
    );
    
    static $trans;

    if (!isset($trans)) {
        $trans = get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT);
        unset($trans['&']);
    }

     $str = strtr($str, $trans);
     return $str;
}

function nl2brHtml($str)
{
	// Normalize
   $str = str_replace("\r\n", "\n", $str);
   $str = str_replace("\r",   "\n", $str);

   // Replace any newlines that aren't preceded by a > with a <br />
   $str = preg_replace('/(?<!>)\n/', "<br />\n", $str);

   return $str;
}