<?php
    $speaker = false;
    foreach ($claimed as $claim) {
        if (isset($claim->userid) && $claim->userid != 0 && user_get_id() == $claim->userid) {
            $speaker = true;
        }
    }


    if ($detail->allow_comments) {
    if (!$auth) {
?>
<p class="info">Want to comment on this talk? <a href="/user/login">Log in</a> or <a href="/user/register">create a new account</a> or comment anonymously</p>
<?php 
    }
        $title='Write a comment';
?>
<a name="comment_form"></a>
<h3 id="comment-form"><?php echo $title; ?></h3>
<?php echo form_open('talk/view/'.$detail->tid . '#comment-form', array('class' => 'form-talk')); ?>

<?php if (!empty($this->validation->error_string)): ?>
    <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
<?php endif; ?>

<div class="row">

    <?php if (!user_is_auth()) {
        $this->load->view('msg_error', array('msg'=>
            'Please note: you are <b>not logged in</b> and will be posting anonymously!'
        )); 
    }
    ?>

    <?php echo form_hidden('edit_comment'); ?>
    <label for="comment">Comment
        <span id="comment_as_user" <?php if (!$auth):?>style="display: none;"<?php endif; ?>> as <a href="/user/view/<?php echo user_get_id(); ?>"><?php echo user_get_username(); ?></a></span>
        <span id="comment_anonymously" <?php if ($auth):?>style="display: none;"<?php endif; ?>> anonymously</span>
    </label>
    <?php 
    

    echo form_textarea(array(
        'name'	=> 'comment',
        'id'	=> 'comment',
        'value'	=> translate_htmlspecialchars($this->validation->comment),
        'cols'	=> 40,
        'rows'	=> 10
    ));
    ?>
<?php if (! $speaker) : ?>
    <label class="checkbox">
        <?php echo form_checkbox('private','1'); ?>
        Mark as private?
    </label>
<?php if ($auth) { ?>
    <label class="checkbox">
        <?php echo form_checkbox('anonymous', '1'); ?>
        Post anonymously?
    </label>
<?php } ?>
    <div class="clear"></div>
<?php endif; ?>
</div>

<?php if ($speaker): ?>
<?php else: ?>

   <?php if ($alreadyRated) : ?>
            <div class="row">
                <label for="rating">Rating</label>
                <div class="rating" id="ratingbar-norating">
                  You already rated this talk.
                </div>
                <div class="rating" id ="ratingbar" style='display:none'>
                    <?php echo rating_form('rating', $this->validation->rating); ?>
                </div>
                <div class="clear"></div>
            </div>


   <?php else: ?>
            <div class="row">
                <label for="rating">Rating</label>
                <div class="rating" style='display:none' id ="ratingbar-norating">
                  You already rated this talk.
                </div>
                <div class="rating" id ="ratingbar">
                    <?php echo rating_form('rating', $this->validation->rating); ?>
                </div>
                <div class="clear"></div>
            </div>

   <?php endif; ?>

<?php endif; ?>

<?php if (!user_is_auth()) { ?>
<div class="row">
    <label for="cinput">Spambot check</label>
    <span>
      <?php echo form_input(array('name' => 'cinput', 'id' => 'cinput'), ""); ?>
      = <b><?php echo $captcha['text']; ?></b>
    </span>
    <div class="clear"></div>
</div>
<?php } ?>

<div class="row row-buttons">
    <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit Comment'); ?>
</div>
<?php 
        echo form_close(); 
        /* close if for date */
} // close comment allowed
