<?php
/**
 * Common helper.
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
 * Escapes the given string for display in a HTML view.
 *
 * @param string $str The string to escape
 *
 * @return string
 */
function escape($str)
{
    // translates all non-unicode entities
    $str = preg_replace('/&(?!(#[0-9]+|[a-z0-9]+);)/si', '&amp;', $str);

    static $trans;

    if (!isset($trans)) {
        $trans = get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT);
        unset($trans['&']);
    }

    $str = strtr($str, $trans);
    return $str;
}

/**
 * Given an array, it'll build out a DOM document for you.
 *
 * When you get the result, you'll need to run a saveXML() on it.
 *
 * @param DOMDocument &$doc   The document to append to.
 * @param array       $data   An array containing elements to append
 * @param DOMDocument $append The data to append
 *
 * @return void
 */
function buildXML(&$doc, $data, $append = null)
{
    foreach ($data as $k => $v) {
        $app = ($append) ? $append : $doc;

        if (is_array($v) || is_object($v)) {
            $c = $app->appendChild($doc->createElement($k));
            buildXML($doc, $v, $c);
        } else {
            $c = $app->appendChild($doc->createElement($k, (string) $v));
        }
    }
}

/**
 * Replaces double line-breaks with paragraph elements.
 *
 * A group of regex replaces used to identify text formatted with newlines and
 * replace double line-breaks with HTML paragraph tags. The remaining
 * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
 * or 'false'.
 *
 * @param string   $pee The text which has to be formatted.
 * @param int|bool $br  Optional. If set, this will convert all remaining
 *                      line-breaks after paragraphing. Default true.
 *
 * @return string Text which has been converted into correct paragraph tags.
 */
function auto_p($pee, $br = 1)
{
    $pee = $pee . "\n"; // just to make things a little easier, pad the end
    $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);

    // space things out a little
    $allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div' .
        '|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math' .
        '|style|input|p|h[1-6]|hr)';
    $pee       = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
    $pee       = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);

    // cross-platform newlines
    $pee = str_replace(
        array(
                    "\r\n", "\r"
        ), "\n", $pee
    );

    if (strpos($pee, '<object') !== false) {
        // no pee inside object/embed
        $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee);
        $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
    }

    // take care of duplicates
    $pee = preg_replace("/\n\n+/", "\n\n", $pee);

    // make paragraphs, including one at the end
    $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
    $pee  = '';
    foreach ($pees as $tinkle) {
        $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
    }

    // under certain strange conditions it could create a P of entirely whitespace
    $pee = preg_replace('|<p>\s*</p>|', '', $pee);
    $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);

    // don't pee all over a tag
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
    $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); //problem with nested lists
    $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
    $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
    if ($br) {
        $pee = preg_replace_callback(
            '/<(script|style).*?<\/\\1>/s',
            create_function(
                '$matches',
                'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'
            ),
            $pee
        );

        // optionally make line breaks
        $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);
        $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
    }

    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
    $pee = preg_replace(
        '!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!',
        '$1',
        $pee
    );

    if (strpos($pee, '<pre') !== false) {
        $pee = preg_replace_callback(
            '!(<pre[^>]*>)(.*?)</pre>!is',
            'clean_pre',
            $pee
        );
    }

    $pee = preg_replace("|\n</p>$|", '</p>', $pee);

    return $pee;
}

/**
 * Function to make HTML presentable, but strip out anything potentially harmful.
 *
 * Can be adapted and evolved as needed this is just a starter for ten
 *
 * @param string $str String to escape
 *
 * @return string
 */
function escape_allowing_presentation_tags($str)
{
    $allowed_tags = "<p>,<br>,<b>,<i>,<strong>,<emphasis>,<em>";
    $str          = strip_tags($str, $allowed_tags);

    return $str;
}


/**
 * Datepicker helper
 *
 * @access	public
 * @param	day
 * @param	month
 * @param	year
 * @return	string
 */
if (! function_exists('form_datepicker')) {
    /**
     * Returns the javascript required for a datepicker for a form
     *
     * @param string $day             Day to select initially in datepicker
     * @param string $month           Month to select initially in datepicker
     * @param string $year            Year to select initially in datepicker
     * @param int    $monthFirstIndex Index of January in the dropdow.
     *
     * @return string
     **/
    function form_datepicker($day, $month, $year, $monthFirstIndex = 0)
    {
        $javascript = <<< JSCRIPT
        <input type='hidden' id='{$day}_{$month}_{$year}' />
    <script type="text/javascript">
    // <![CDATA[
        $(function() {
            var yr=$('select[name={$year}]');
            var mo=$('select[name={$month}]');
            var da=$('select[name={$day}]');
            var b4show = function(input, dpicker) {
                var dte = '' + padstring(mo.val(),2)
                    +'-'+padstring(da.val(),2)+'-'+yr.val();
                $(input).val(dte);
            };
            var selectdte = function(dateText, inst) {
                var vals = dateText.split('-');
                yr.val(vals[2]);
                mo.get(0).selectedIndex = (vals[0]-1 + {$monthFirstIndex});
                da.val(vals[1].replace(/^0/, ""));
            };
            $('#{$day}_{$month}_{$year}').datepicker({
                showOn: "button",
                buttonImage: "/inc/img/datepicker.gif",
                buttonImageOnly: true,
                beforeShow: b4show,
                dateFormat: 'mm-dd-yy',
                onSelect: selectdte
            });
        });
    // ]]>
    </script>
JSCRIPT;
        return $javascript;
    }
}

/**
 * Remove htmlspecialchars from a string, used for comments
 * Because CI has decided to embed htmlspecialchars in the form helper...
 *
 * @param string $comment Comment to decode
 *
 * @return string
 */
function translate_htmlspecialchars($comment)
{
    $comment = htmlspecialchars_decode($comment);
    $comment = str_replace(array("&#39;", "&quot;"), array("'", '"'), $comment);
    return $comment;
}
// --------------------------------------------------------------------
