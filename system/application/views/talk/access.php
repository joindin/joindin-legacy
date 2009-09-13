<?php
menu_pagetitle('Speaker: talk access');

// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');

$this->load->helper('text');
?>

<div class="menu">
	<ul>
		<li><a href="/talk/view/<?= $talk->getId(); ?>">Talk details</a></li>
        <li><a href="/talk/sessions/<?= $talk->getId(); ?>">Talk sessions</a></li>
        <li><a href="/talk/statistics/<?= $talk->getId(); ?>">Talk statistics</a></li>
		<li class="active"><a href="/talk/access/<?= $talk->getId(); ?>">Talk access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<?php
// Load message area's
$this->load->view('message/area');
$this->load->view('message/flash');
?>

<div class="box">
    
    <h1 class="blue"><?= escape($talk->getTitle()); ?></h1>
    <table class="data-table">
        <thead>
            <tr>
                <td style="width: 110px;">Access token</td>
                <td>Description</td>
                <td style="width: 110px;">Created</td>
                <td style="width: 110px;">&nbsp;</td>
            </tr>
        </thead>
        <tbody>
            <?php if($talk->getTokenCount() == 0) : ?>
            <tr>
                <td colspan="4">No tokens found.</td>
            </tr>
            <?php else : foreach($talk->getTokens() as $token) : ?>
            <tr>
                <td><a href="/talk/token/<?= $token->getId(); ?>"><?= $token->getAccessToken(); ?></a></td>
                <td><?= escape(word_limiter($token->getDescription(), 100, '')); ?>
                <td><?= date('m/d/y', $token->getCreated()); ?></td>
                <td>
                    <a class="btn-small" href="/talk/edittoken/<?= $token->getId() ?>">edit</a>
	    			&nbsp;or&nbsp;
	    			<?= delete_link(
	    				'/talk/deltoken/' . $token->getId(), 
	    				'Are you sure you want to delete token ' . $token->getAccessToken() . '?') 
	    			?>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
    
    <p>
	    <a class="btn btn-green" href="/talk/addtoken/<?= $talk->getId(); ?>">Add token</a>
    </p>
    
    <div class="bluebar right">
		<a href="/speaker/talks">back to talks</a>
	</div>
</div>