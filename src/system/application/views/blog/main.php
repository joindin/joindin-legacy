<?php 
menu_pagetitle('Blog');
?>
<h1 class="icon-event">
    <?php if (user_is_admin()) { ?>
    <span style="float:left">
    <?php } ?>
    Blog
    <?php if (user_is_admin()) { ?>
    </span>
    <?php } ?>
    <?php if (user_is_admin()) { ?>
    <a class="btn" style="float:right" href="/blog/add">Add blog post</a>
    <div class="clear"></div>
    <?php } ?>
</h1>

<?php if (isset($posts) && count($posts)>0): ?>
    <?php foreach ($posts as $k=>$v): ?>
<div class="row row-blog">
    <h2 class="h3"><a href="/blog/view/<?php echo $v->ID; ?>"><?php echo $v->title; ?></a></h2>
    <div class="desc">
        <?php echo auto_p(auto_link($v->content)); ?>
    </div>
    <p class="opts">
        <a href="/blog/view/<?php echo $v->ID; ?>#comments"><?php echo $v->comment_count; ?> comment<?php echo $v->comment_count == 1 ? '' : 's'?></a> |
         Written <strong><?php echo date('d.M.Y', $v->date_posted); ?></strong> at <strong><?php echo date('H:i', $v->date_posted); ?></strong> (<?php echo $v->author_id; ?>)
    </p>
    
    <?php if (user_is_admin()): ?>
    <div class="admin">
        <a class="btn-small" href="/blog/edit/<?php echo $v->ID; ?>">Edit</a>
        <a class="btn-small" href="/blog/delete/<?php echo $v->ID; ?>">Delete</a>
    </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>

    <?php endforeach; ?>
<?php else: ?>
    <?php $this->load->view('msg_info', array('msg' => 'No posts yet! Come back soon!')); ?>
<?php endif; 
