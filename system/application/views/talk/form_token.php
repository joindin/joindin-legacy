<?php
menu_pagetitle('Talk: access token');

// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');

// Load message area's
$this->load->view('message/area');
$this->load->view('message/flash');
?>

<div class="menu">
	<ul>
		<li><a href="/talk/view/<?= $talk->getId(); ?>">Talk details</a></li>
        <li><a href="/talk/sessions/<?= $talk->getId(); ?>">Talk sessions</a></li>
        <li><a href="/talk/statistics/<?= $talk->getId(); ?>">Talk statistics</a></li>
		<li><a href="/talk/access/<?= $talk->getId(); ?>">Talk access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<div class="box">
    <?php
    if($token->isNew()) {
        echo form_open('/talk/addtoken/' . $talk->getId());
    } else {
        echo form_open('/talk/edittoken/' . $token->getId());
    }
    ?>
    
    <div class="row">
        <label for="desctipion">Description</label>
        <input type="text" id="description" name="description" value="<?= $token->getDescription(); ?>" />
        <div class="clear"></div>
    </div>
    
    <div class="row row-buttons">
        <input class="btn" type="submit" id="save" name="save" value="Save" />
        &nbsp;or&nbsp;<a href="/talk/access/<?= $talk->getId(); ?>">cancel</a>
        <div class="clear"></div>
    </div>
    
    <?= form_close(); ?>
</div>
