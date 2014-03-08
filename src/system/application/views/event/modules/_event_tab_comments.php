<div id="comments">

<?php
$msg=$this->session->flashdata('msg');
if (!empty($msg)): 
?>
    <?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php if (count($comments) == 0): ?>
    <?php $this->load->view('msg_info', array('msg' => 'No comments yet.')); ?>
<?php else: ?>

    <?php 
    foreach ($comments as $k => $comment):
        if ($comment->user_id != 0) {
            $uname = '<strong><a href="/user/view/'.$comment->user_id.'">'.escape($comment->cname).'</a> ('.$comment->user_comment_count.' comments)</strong>';
        } elseif (isset($comment->cname)) {
            $uname = '<strong>'.escape($comment->cname).'</strong>';
        } else {
            $uname = "<span class=\"anonymous\">Anonymous</span>";
        }
        $type	= ($event_detail->event_start>time()) ? 'Suggestion' : 'Feedback';
    ?>
    <div id="comment-<?php echo $comment->ID ?>" class="row row-event-comment">
        <div class="text">
            <p class="info">
                <strong><?php echo $comment->display_datetime; ?></strong> by <strong><?php echo $uname; ?></strong>
                <?php echo !empty($comment->source)?"via ".escape($comment->source) : "" ?>
                (<?php echo escape($type); ?>)
            </p>
            <div class="desc">
                <?php echo auto_p(escape($comment->comment)); ?>
            </div>
            <?php if ($admin): ?>
                <a class="btn-small delete-evt-commment" id="<?php echo $comment->ID.'_'.$comment->event_id; ?>" href="#">delete</a>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
    <?php endforeach; ?>
<?php endif;

$adv_mo=strtotime('+3 months', $event_detail->event_start);
if (time()<$adv_mo): ?>

    <h3 id="comment-form">Write a comment</h3>
    <?php echo form_open('event/view/'.$event_detail->ID.'#comment-form', array('class' => 'form-event')); ?>

    <?php if (!empty($this->validation->error_string)): ?>
        <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>

    <?php
    
    $types=array(
        'Suggestion'		=> 'Suggestion',
        'General Comment'	=> 'General Comment',
        'Feedback'			=> 'Feedback'
    );
    
    $type=($event_detail->event_start>time()) ? 'Suggestion':'Feedback';

    ?>

    <div class="row">
        <label for="type">Type</label>
        <div class="input"><?php echo $type?></div>
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="event_comment">Comment</label>
        <?php 
        $arr = array(
                'name'=>'event_comment',
                'id'=>'event_comment',
                'value'=>translate_htmlspecialchars($this->validation->event_comment),
                'cols'=>40,
                'rows'=>10
        );
        echo form_textarea($arr);
        ?>
        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="cinput">Spambot check</label>
        <span>
          <?php echo form_input(array('name' => 'cinput', 'id' => 'cinput'), ""); ?>
          = <b><?php echo $captcha['text']; ?></b>
        </span>
        <div class="clear"></div>
    </div>
    
    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Submit Comment'); ?>
    </div>
    <?php endif; echo form_close(); ?>
</div>
