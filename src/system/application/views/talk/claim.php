<?php
//echo '<pre>'; print_r($claims); echo '</pre>';

echo form_open('talk/claim');

if (isset($approved) && $approved>0) {
    echo '<div><b>'.$approved.' Talk Claims Approved!</b></div><br/>';
}
if (isset($deleted) && $deleted>0) {
    echo '<div><b>'.$deleted.' Talk Claims Removed!</b></div><br/>';
}
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="center"><b>Good</b></td>
    <td align="center"><b>Del</b></td>
    <td><b>Talk Info</b></td>
    <td><b>Speaker</b></td>
    <td><b>Claim By</b></td>
</tr>
<?php
foreach ($claims as $k=>$v) {
    $name=(empty($v->claiming_name)) ? $v->claiming_user : $v->claiming_name;
    echo '<tr><td align="center">'.form_checkbox('claim_'.$v->ua_id,1).'</td>';
    echo '<td align="center">'.form_checkbox('del_claim_'.$v->ua_id,1).'</td>';
    echo sprintf('
        <td><a href="/talk/view/%s">%s</a></td>
        <td>%s</td>
        <td><a href="/user/view/%s">%s</a></td>
    </tr>
    ', $v->talk_id, $v->talk_title, $v->speaker, $v->uid, $name);
}
?>
<tr>
    <td colspan="5" align="right">
        <?php echo form_submit('sub','Update Claim Status'); ?>
    </td>
</tr>
</table>
<?php echo form_close(); 
