<?php
/* 
 * Add/create a speaker profile access
 * 
 */

menu_pagetitle('Manage Speaker Profile Access');

$this->load->view('user/_nav_sidebar');

$edit_select=array();
if ($curr_access) {
    foreach ($curr_access as $curr) { $edit_select[]=(is_object($curr)) ? $curr->field_name : $curr; }
}
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

$path=($req_type=='edit') ? 'speaker/access/edit/'.$token_id : 'speaker/access/add';
echo form_open($path, array('id'=>'frm_access_add'));
?>

<div id="box">
    <div class="row">
        <label for="token_name">Token Name</label>
        <?php echo form_input('token_name', $this->validation->token_name); ?>
        <span style="color:#3567AC;font-size:11px">
        <b>What's a token?</b> Think of a token as a shortcut to get to this access profile. You
        can name your tokens just about anything you want - something easy to remember usually works best.
        More information on tokens is over in <a href="/help/manage_user_acct#tokens">our Help section</a>. Note: 
        token names can only use letters and numbers.
        </span>
        <div class="clear"></div>
    </div>
    <div class="row">
        <label for="token_desc">Token Description</label>
        <?php echo form_input('token_desc', $this->validation->token_desc); ?>
        <div class="clear"></div>
    </div>
    <div class="row">
        <label for="access_items"></label>
        <?php
        $i=0;
        echo '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>';
        foreach ($fields as $k=>$v) {
            $i++;
            echo '<td style="padding:3px">';
            //echo '<input type="checkbox" value="'.$k.'" name="fields[]"> '.$v.'</td>';
            $is_chk=(in_array($k, $edit_select)) ? true : false;
            echo form_checkbox('fields[]', $k, $is_chk).' '.$v."\n";
            if ($i%2==0) { echo '</tr><tr>'; }
        }
        echo '</tr></table>';
        ?>
        <div class="clear"></div>
    </div>
    <div class="row">
        <label for="token_desc">Make Public</label>
        <?php echo form_checkbox('is_public',1, $this->validation->is_public)." Check to make this access profile public.\n"; ?><br/>
        <span style="color:#3567AC;font-size:11px">
        <b>Make Public</b>: Marking this profile as public makes it viewable on your user profile page to any viewer.
        </span>
        <div class="clear"></div>
    </div>
    <div class="row">
        <label for="event"></label>
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Save changes'); ?>
        <div class="clear"></div>
    </div>
    <?php if (isset($token_id)) { echo form_hidden('token_id', $token_id); } ?>
</div>
<?php echo form_close(); ?>
