<?php
menu_pagetitle('Speaker access');
// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<div class="menu">
	<ul>
		<li><a href="/speaker/profile">Speaker Profile</a></li>
		<li class="active"><a href="/speaker/access">Profile Access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<?php
// Add message area
$this->load->view('message/area');
// Catch flash messages
$this->load->view('message/flash');
?>

<div class="box">
    <p>
	    some introductary text here ...
    </p>

    <table cellpadding="0" cellspacing="0" class="data-table">
	    <thead>
		    <tr>
			    <td>Token</td>
			    <td style="width: 110px;">Created</td>
			    <td style="width: 100px;">&nbsp;</td>
		    </tr>
	    </thead>
	    <tbody>
		<?php if($talk->getTokenCount() == 0) : ?>
			<tr>
				<td colspan="3">No tokens found.</td>
			</tr>
	    <?php else : foreach($speaker->getTokens() as $token) : ?>
	        <tr>
	            <td><a href="/speaker/token/<?= $token->getId() ?>"><?= $token->getAccessToken(); ?></a></td>
	            <td><?= date('m/d/Y', $token->getCreated()); ?></td>
	            <td>
					<a class="btn-small" href="/speaker/edittoken/<?= $token->getId() ?>">edit</a>
	    			&nbsp;or&nbsp;
	    			<?= delete_link(
	    				'/speaker/deltoken/' . $token->getId(), 
	    				'Are you sure you want to delete token ' . $token->getAccessToken() . '?') 
	    			?>
				</td>
	        </td>
	    <?php endforeach; endif; ?>
	    </tbody>
    </table>
    
    <p style="text-align: right;">
	    <a class="btn btn-green" href="/speaker/edittoken">Add token</a>
    </p>

</div>


