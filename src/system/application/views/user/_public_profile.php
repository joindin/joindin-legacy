<h2>Profile</h2>
<?php
//echo '<pre>'; print_r($profile); echo '</pre>';
$data=(isset($profile['data'])) ? $profile['data'] : array();

function setchk($arr, $key, $label=null) {
    if ($label && isset($arr[$key])) { echo '<b>'.$label.':</b> '; }
    echo (isset($arr[$key])) ? $arr[$key] : null;
}
?>
<table cellpadding="0" cellspacing="0" border="0">

<!--<tr><td><?php setchk($data,'full_name'); ?></td></tr>-->
<tr><td><?php setchk($data,'contact_email','Contact Email'); ?></td></tr>
<tr><td><?php setchk($data,'phone','Phone'); ?></td></tr>
<tr>
    <td>
        <?php if (!empty($profile['data']['city']) && !empty($profile['data']['city'])): ?>
        <?php setchk($data,'street_address'); ?><br/>
        <?php setchk($data,'city'); ?>, <?php setchk($data,'state'); ?>
        <?php setchk($data,'zip'); ?>
        <?php endif; ?>
    </td>
</tr>
<tr><td><?php if (!empty($profile['data']['blog'])) { echo '<b>Blog:</b> '.$profile['data']['blog']; } ?></td></tr>
<tr><td><?php if (!empty($profile['data']['website'])) { echo '<b>Website:</b> '.$profile['data']['website']; } ?></td></tr>

<tr><td><?php setchk($data,'job_title','Job Title'); ?></td></tr>
<tr><td><?php setchk($data,'bio','Bio'); ?></td></tr>

</table>

<!--
Full Name 	 Contact Email
Website 	Blog
Phone 	City
Zip 	Street Address
Job Title 	Bio
Picture
-->
