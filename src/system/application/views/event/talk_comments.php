<?php
if (empty($comments) && $page == 1) {
    $this->load->view('msg_info', array('msg' => 'No comments yet.'));
} else {
    foreach ($comments as $k => $v) {
        $class = '';
        if (isset($v->user_id) && $v->user_id != 0) {
            $displayname = (empty($v->full_name)) ? $v->uname : $v->full_name;
            $uname = '<a href="/user/view/'.$v->user_id.'">'.escape($displayname).'</a> ('.$v->user_comment_count.' comments)';
        } else { 
            $uname = '<span class="anonymous">Anonymous</span>';
            $class .= ' row-talk-comment-anonymous';
        }

        $talk_title = '<a href="/talk/view/'.$v->tid.'#comments">'.escape($v->talk_title).'</a>';

?>
<div id="comment-<?php echo $v->ID ?>" class="row row-talk-comment<?php echo $class?>">
    <div class="img">
    <?php if ($v->rating > 0) echo rating_image($v->rating); ?><br/>
    <?php if (!empty($v->twitter_username)): ?>
    <a href="http://twitter.com/<?php echo $v->twitter_username; ?>"><img src="/inc/img/twitter_share_icon.gif" style="margin-top:10px" width="20"/></a>
    <?php endif; ?>
    <?php if (!empty($v->gravatar)) {
    echo '<a href="/user/view/'.$v->user_id.'"><img src="'.$v->gravatar.'" height="45" align="right" style="margin:10px"/></a>'; }
    ?>
    </div>
    <div class="text">
    <p class="info">
            <strong><?php echo $v->display_datetime?></strong> by <strong><?php echo $uname; ?></strong>
            <?php echo !empty($v->source)?"via ".escape($v->source) : "" ?>
        <?php if ($v->private == 1): ?>
            <span class="private">Private</span>
        <?php endif; ?>
        </p>
        <p class="info"> on <strong><?php echo $talk_title; ?></strong></p>
        <div class="desc">
            <?php echo auto_p(escape($v->comment)); ?>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php
    }

    if ($moreComments) {
?>
    <a id="more-talk-comments" href="#" onclick="JI_event.loadMoreTalkComments(); return false;">Load more comments</a>
<?php
    }
}
?>
