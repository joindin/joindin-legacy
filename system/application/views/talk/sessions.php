<?php
menu_pagetitle('Speaker: talk sessions');

$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>
<div class="menu">
	<ul>
		<li><a href="/talk/view/<?= $talk->getId(); ?>">Talk details</a></li>
        <li class="active"><a href="/talk/sessions/<?= $talk->getId(); ?>">Talk sessions</a></li>
        <li><a href="/talk/statistics/<?= $talk->getId(); ?>">Talk statistics</a></li>
		<li><a href="/talk/access/<?= $talk->getId(); ?>">Talk access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<div class="box">
    
    <h1 class="blue"><?= escape($talk->getTitle()); ?></h1>
    
    <div>
        <?php if($talk->getSessionCount() == 0) : ?>
        <p>
            No sessions given for this talk.
        </p>
        <?php else : 
        foreach($talk->getSessions() as $session) {
            $this->load->view('session/_session-row', array('session' => $session));
        }
        endif; ?>
    </div>
    
    <div class="bluebar right">
        <a href="/speaker/talks">back to talks</a>
    </div>
    
</div>