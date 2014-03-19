<?php
$this->load->view('user/_nav_sidebar', array(
        'pending_events' => $pending_events
    )
);
?>
<div class="menu">
    <ul>
        <li><a href="/user/main">Dashboard</a>
        <li><a href="/user/manage">Manage Account</a>
    <?php if (user_is_admin()): ?>
        <li class="active"><a href="/user/admin">User Admin</a>
        <li><a href="/event/pending">Pending Events</a>
    <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<?php 
echo form_open('user/admin', array('id'=>'userAdminForm'));
echo form_input('user_search', $this->validation->user_search);
echo form_submit('sub','Search');
//echo form_button('clear','Clear','onClick="document.location.href=\'/user/admin\';"');
//echo form_close();
?>

<br/>
<b>Paging:</b>
<?php if ($paging): ?>
<?php echo $paging; ?>
<?php else: ?>
<strong>1</strong>
<?php endif; ?>

<br/>
<?php
if (empty($msg)) {
    $msg=$this->session->flashdata('msg');
}
else
{
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php
}
?>

<?php echo 'show: '.form_dropdown('users_per_page', array(
    '10' => '10 records',
    '20' => '20 records',
    '40' => '40 records'
), $this->validation->users_per_page,'id="showLimit"'); ?>

<table summary="" class="list" width="100%">
<tr class="header">
    <th></th>
    <th>Username</th>
    <th>Detail</th>
    <th>Is Admin?</th>
    <th>Last Login</th>
    <th>Status</th>
</tr>
<?php
$ct=0;
foreach ($users as $k=>$v) {
    $class 		= ($ct%2==0) ? 'row1' : 'row2';
    $is_admin	= ($v->admin==1) ? '<b style="color:#00E200">Y</b>' : '<b style="color:#FF0000">N</b>';
    $last_log	= (!empty($v->last_login)) ? date('m.d.Y H:i:s', $v->last_login): '';
    $active		=  'inact';
    $activeChange =  'activate';
    if (!empty($v->active) && $v->active==1) {
        $active = 'act';
        $activeChange = 'deactivate';
    }
    echo sprintf('
        <tr class="%1$s">
            <td><input type="checkbox" name="sel[]" value="%2$s"/></td>
            <td><a href="/user/view/%2$s">%3$s</a></td>
            <td>%4$s<br/>
                <a href="mailto:%5$s">%5$s</a>
            </td>
            <td align="center">%6$s <a href="/user/changeastat/%2$s/admin" id="admin_link_%2$s">toggle</a></td>
            <td>%7$s</td>
            <td align="right">%8$s <a href="/user/changestat/%2$s/admin" id="status_link_%2$s">%9$s</a></td>
        </tr>
    ', $class, $v->ID, escape($v->username), escape($v->full_name), escape($v->email),
    $is_admin, $last_log, $active, $activeChange);
    $ct++;
}
?>
<tr>
    <td colspan="6">
        <?php echo form_submit('um','Delete Selected'); ?>
    </td>
</tr>
</table>
<?php echo form_close(); ?>

<br/>
<b>Paging:</b>
<?php if ($paging): ?>
<?php echo $paging; ?>
<?php else: ?>
<strong>1</strong>
<?php endif; 
