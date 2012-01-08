<?php 
$v=$details[0];
$bid=$v->ID;
if (isset($full_name)) { $this->validation->name=escape($full_name); }
 
menu_pagetitle('Blog: ' . escape($v->title));
?>
<script src="/inc/js/blog.js"></script>
<div class="detail">

    <h1><?php echo $v->title?></h1>

    <p class="info">
        Written <strong><?php echo date('M j, Y', $v->date_posted); ?></strong> at <strong><?php echo date('H:i', $v->date_posted); ?></strong> (<?php echo $v->author_id; ?>)
    </p>

    <div class="desc">
        <?php echo auto_p(auto_link($v->content)); ?>
    </div>
</div>

<?php if (user_is_admin()): ?>
<p class="admin">
    <a class="btn-small" href="/blog/edit/<?php echo $v->ID; ?>">Edit post</a>	
    <a class="btn-small" href="/blog/delete/<?php echo $v->ID; ?>">Delete post</a>
</p>
<?php endif; ?>

<?php
if (empty($msg)) {
    $msg=$this->session->flashdata('msg');
}
if (!empty($msg)): 
?>
    <?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<script type="text/javascript"> blog.init(); </script>
