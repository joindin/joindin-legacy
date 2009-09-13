<?php 
menu_pagetitle('Speaker: Sessions');

// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<h1>My Sessions</h1>
<p>
    This is an overview of all the sessions you have given. This includes sessions
    connected to any of your talks.
</p>

<?php if(count($speaker->getSessions()) === 0) : ?>
<p>
    Their would be a list here if you'd have given any sessions.
</p>
<?php else : ?>
<?php
foreach($speaker->getSessions() as $session) {
    $this->load->view('session/_session-row', array('session' => $session));
}
?>

<?php endif; ?>

