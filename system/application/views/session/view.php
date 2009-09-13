<?php
menu_pagetitle('Session: ' . escape($session->getTitle()));
$this->load->view('message/flash');
?>

<div class="detail">

    <h1><?= $session->getTitle() ?></h1>

	<p class="info">
		<strong><?= $session->getSpeaker() ?></strong> (<?= date('M j, Y',$session->getDate()); ?>)
		<br/> 
		<?php echo escape($session->getCategory()); ?> 
		at <strong><a href="/event/view/<?= $session->getEventId(); ?>"><?= escape($session->getEventTitle()); ?></a></strong> 
		(in <?= escape($session->getLanguage());?>)
	</p>
	
	<p class="rating">
		<?= rating_image($session->getRating()); ?>
	</p>

	<div class="desc">
		<?= auto_p(auto_link(escape($session->getDescription()))); ?>
	</div>
	
	<p class="quicklink">
		Quicklink: <strong><a href="http://joind.in/<?= $session->getId() ?>">http://joind.in/<?= $session->getId() ?></a></strong>
	</p>
	
	<div class="clear"></div>

</div>

<?php if(user_is_admin() || (!$session->isClaimed() && user_is_authenticated())):?>
<p class="admin">
<?php
if(!$session->isClaimed() && user_is_authenticated()): ?>
    <!-- onClick="claimTalk(<?= $session->getId() ?>)" -->
	<a class="btn-small" href="/claim/session/<?= $session->getId() ?>" id="claim_btn">Claim this Session</a>	
<?php endif; ?>
<?php if(user_is_admin()):?>
    &nbsp;||&nbsp;
	<a class="btn-small" href="/session/edit/<?= $session->getId() ?>">Edit session</a>
	&nbsp;|&nbsp;
	<a class="btn-small" href="/session/delete/<?= $session->getId() ?>">Delete session</a>
    &nbsp;||&nbsp;
<?php endif; ?>
</p>
<?php endif; ?>

<p class="ad">
    <script type="text/javascript"><!--
        google_ad_client = "pub-2135094760032194";
        /* 468x60, created 11/5/08 */
        google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</p>

<div class="box">

    <h2 id="comments">Comments</h2>
    <?php 
    if($session->getCommentCount() == 0) {
        $this->load->view('message/info', array('message' => 'Not comments yet'));
    } else {
        foreach($session->getComments() as $comment) {
            $this->load->view('session/_session-comment.php', array('comment' => $comment));
        }
    }
    ?>
</div>

<h3 id="comment-form">Write a comment</h3>

<?php if(!$session->isOpenForComments()) : ?> 
<p class="info">
    Currently not open for comment.
</p>
<?php elseif(!user_is_authenticated()): ?>
<p class="info">
    Want to comment on this session? <a href="/user/login">Log in</a> or <a href="/user/register">create a new account</a>.
</p>
<?php else : ?>

<?= form_open("session/view/{$session->gettId()}#comment-form", array('class' => 'form-talk')); ?>

<?php if(isset($error) && !empty($error)) {
    $this->load->view('message/error', array('message' => $error));
} ?>

<div class="row">
	<label for="comment">Comment</label>
	<?= form_textarea(array(
	    'name' => 'comment',
        'id' => 'comment',
		'value' => (isset($newComment) ? $newComment->getComment() : ''),
		'cols' => 40,
		'rows' => 10
	)); ?>
	<div class="clear"></div>
</div>

<div class="row">	
	<label class="checkbox">
        <?= form_checkbox('private', '1'); ?>
        Mark as private?
    </label>
	<div class="clear"></div>
</div>

<?php if(user_get_id() != $session->getSpeakerId()) : ?>
<div class="row">
	<label for="rating">Rating</label>
	<div class="rating">
	    <?php echo rating_form('rating', (isset($newComment) ? $newComment->getRating() : 0)); ?>
	</div>
	<div class="clear"></div>
</div>
<?php endif; ?>

<div class="row row-buttons">
	<?= form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit Comment'); ?>
</div>

<?= form_close() ?>

<?php endif; ?>

