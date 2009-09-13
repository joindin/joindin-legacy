<?php 
// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<?php 
// Catch flash messages
$this->load->view('message/flash');

// Catch other errors and messages
if(isset($message) && !empty($message)) {
    $this->load->view('message/info', array('message' => $message)); 
}
if(isset($error) && !empty($error)) {
    $this->load->view('message/error', array('message' => $error)); 
}
?>
<h1>Dashboard</h1>

<div class="box">
    <h2>My Sessions</h2>
    <?php if (count($user->getSessions()) === 0): ?>
	<p>You did not give any sessions yet</p>
    <?php else: foreach($user->getSessions() as $session) : 
        	$this->load->view('session/_session-row', array('session' => $session));
        endforeach; endif; ?>
	<div class="clear"></div>
</div>

<div class="box">
    <h2>My Events</h2>
    <?php if(count($user->getAttendance()) === 0) : ?>
    <p>You have not attended any events yet.</p>
    <?php else: foreach($user->getAttendance() as $attendance) : 
        $this->load->view('event/_event-row', array('event' => $attendance->getEvent()));
    endforeach; endif; ?>
</div>

<div class="box">
    <h2>My Comments</h2>
    <?php if (count($user->getSessionComments()) === 0): ?>
	<p>You have not made any comments yet</p>
    <?php else: foreach($user->getSessionComments() as $comment): ?>
    
    <div class="row">
    	<?= date('M j, Y H:i', $comment->getDate()) ?> on
    	<strong>
    	    <a href="/session/view/<?= $comment->getSessionId(); ?>#comment-<?= $comment->getId(); ?>">
    	        <?= escape($comment->getSessionTitle()); ?>
    	    </a>
    	</strong> (from <a href="/event/view/<?= $comment->getSession()->getEventId() ?>"><?= escape($comment->getSession()->getEventTitle()); ?></a>)
    	<div class="clear"></div>
    </div>
    <?php endforeach; endif; ?>
</div>

