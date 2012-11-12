<?php
/**
 * Timezone helper.
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
 * Assemble a custom timezone menu.
 *
 * @param string $name       The name of this menu
 * @param string $valueCont  The selected continent
 * @param string $valuePlace The selected city
 *
 * @return string
 */
function custom_timezone_menu($name, $valueCont = null, $valuePlace = null)
{
    $hname        = htmlentities($name);
    $allTimezones = DateTimeZone::listIdentifiers();
    $tzs          = array();
    $validConts   = array(
        'Africa',
        'America',
        'Antarctica',
        'Arctic',
        'Asia',
        'Atlantic',
        'Australia',
        'Europe',
        'Indian',
        'Pacific'
    );

    foreach ($allTimezones as $timezone) {
        if (substr_count($timezone, '/') !== 1) {
            continue;
        }

        list($cont, $city) = explode('/', $timezone, 2);
        if (in_array($cont, $validConts)) {
            $tzs[$cont][] = $city;
        }
    }

    $str   = '';
    $str  .= '<select onchange="change_cont_' . $hname . '(this);" name="' .
        $hname . '_cont" id="' . $hname . '_cont">';
    $str  .= '<option value="">Please select a continent</option>';
    $conts = array_keys($tzs);

    foreach ($conts as $cont) {
        $str .= '<option value="' . htmlentities($cont) . '"';
        if ($valueCont == $cont) {
            $str .= 'selected="selected"';
        }
        $str .= '>' . htmlentities($cont) . '</option>';
    }
    $str .= '</select>';

    $str .= '<select name="' . $hname . '_place" id="' . $hname . '_place">';
    $str .= '<option value="">&lt;= Please choose a continent first</option>';
    $str .= '</select>';

    $str .= <<<EOT
    <script type="text/javascript">
         $(document).ready(function() {
            change_cont_$hname();
        });
        function change_cont_$hname() {
            var cont = document.getElementById("${hname}_cont");
            var placeSelect = document.getElementById("${hname}_place");
            placeSelect.options.length = 0;
            $(placeSelect).show();
            switch(cont.value) {

EOT;
    foreach ($conts as $cont) {
        sort($tzs[$cont]);
        $str .= 'case "' . htmlentities($cont) . '":' . "\n";
        foreach ($tzs[$cont] as $num => $place) {
            $str .= 'placeSelect.options[placeSelect.options.length] = ' .
                'new Option("' . htmlentities($place) . '", "' .
                htmlentities($place) . '");' . "\n";
            if ($valuePlace == $place) {
                $str .= 'placeSelect.options[placeSelect.options.length - 1]' .
                    '.selected=true;' . "\n";
            }
        }

        $str .= 'break;' . "\n";
    }
    $str .= <<<EOT
                case "":
                    $(placeSelect).hide();
                    placeSelect.options[placeSelect.options.length] = 
                        new Option("<= Please choose a continent first");
                    break;
                default:
                    notifications.alert('Unknown continent, please '+
                        'select a valid continent');
                    break;
            }
        }
    </script>

EOT;
    $str .= '<br />';

    return $str;
}

