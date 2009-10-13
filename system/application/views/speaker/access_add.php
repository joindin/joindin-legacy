<?php
/* 
 * Add/create a speaker profile access
 * 
 */

menu_pagetitle('Manage Speaker Profile Access');

$this->load->view('user/_nav_sidebar');

?>

<div class="menu">
	<ul>
	<li><a href="/speaker/profile">Speaker Profile</a>
	<li class="active"><a href="/speaker/access">Profile Access</a>
	</ul>
	<div class="clear"></div>
</div>

<p>
    Select the items from the list below to include in this Speaker Profile Access:
</p>

<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif;

$fields=array(
    'full_name'=>'Full Name','contact_email'=>'Contact Email','website'=>'Website',
    'blog'=>'Blog','phone'=>'Phone','city'=>'City','zip'=>'Zip','street'=>'Street Address',
    'job_title'=>'Job Title','bio'=>'Bio','picture'=>'Picture'
);

echo form_open('speaker/access/add',array('id'=>'frm_access_add'));
$i=0;
echo '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>';
foreach($fields as $k=>$v){
    $i++;
    echo '<td style="padding:3px">';
    //echo '<input type="checkbox" value="'.$k.'" name="fields[]"> '.$v.'</td>';
    echo form_checkbox('fields[]',$k).' '.$v."\n";
    if($i%2==0){ echo '</tr><tr>'; }
}
echo '</tr>';
?>
<tr><td style="padding:5px" align="center" colspan="2">
    <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Save changes'); ?>
</td></tr>
</table>
<?php echo form_close(); ?>