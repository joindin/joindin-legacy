<?php 
menu_pagetitle('Talk: ' . escape($talk->getTitle())); 

// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<div class="menu">
	<ul>
		<li class="active"><a href="/talk/view/<?= $talk->getId(); ?>">Talk details</a></li>
        <li><a href="/talk/sessions/<?= $talk->getId(); ?>">Talk sessions</a></li>
        <li><a href="/talk/statistics/<?= $talk->getId(); ?>">Talk statistics</a></li>
		<li><a href="/talk/access/<?= $talk->getId(); ?>">Talk access</a></li>
	</ul>
	<div class="clear"></div>
</div>


<div class="box detail">

    <h1><?= escape($talk->getTitle()); ?></h1>
    
    <?= auto_p(escape($talk->getDescription())) ?>

    <h2>Abstract</h2>
    <?php if($talk->getAbstract() != '') :
        echo auto_p(escape($talk->getAbstract()));
    else : ?>
        <p>
            No abstract available.
        </p>
    <?php endif; ?>
</div>

<div class="right" style="margin-bottom: 20px;">
    <a class="btn btn-green" href="/talk/edit/<?= $talk->getId() ?>">edit</a>&nbsp;or&nbsp;
    <a href="/talk/delete/<?= $talk->getId() ?>">delete</a>
</div>

<div class="bluebar right">
    <a href="/speaker/talks">back to talks</a>
</div>