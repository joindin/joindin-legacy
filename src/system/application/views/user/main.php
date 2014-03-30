<?php
$this->load->view('user/_nav_sidebar', array(
        'pending_events' => $pending_events
    )
);
?>
<?php if (!empty($this->validation->error_string)): ?>
    <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
<?php endif; ?>
<div class="menu">
    <ul>
        <li class="active"><a href="/user/main">Dashboard</a></li>
        <li><a href="/user/manage">Manage Account</a></li>
    <?php if (user_is_admin()): ?>
        <li><a href="/user/admin">User Admin</a></li>
        <li><a href="/event/pending">Pending Events</a></li>
    <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<?php 
if (empty($msg)) {
    $msg=$this->session->flashdata('msg');
}
if (!empty($msg)): 
?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php if ($gravatar): ?>
    <img src="<?php echo $gravatar; ?>" style="margin-bottom:5px" height="80" width="80" /><br/>
<?php endif; ?>

<div class="box">
    <h2>My Talks</h2>
<?php if (count($talks) == 0): ?>
    <p>No talks so far</p>
<?php else: ?>
    <?php
        foreach ($talks as $k=>$v) {
            $this->load->view('talk/_talk-row', array('talk'=>$v));
        }
    ?>
<?php endif; ?>
    <div class="clear"></div>
</div>


<div class="box">
    <h2>My Comments</h2>
<?php if (count($comments) == 0): ?>
    <p>No comments so far</p>
<?php else: ?>
    <?php foreach ($comments as $k=>$v): ?>
    <div class="row">
        <strong><a href="/talk/view/<?php echo $v->talk_id; ?>#comment-<?php echo $v->ID; ?>"><?php echo escape($v->talk_title); ?></a></strong>
        <div class="clear"></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
