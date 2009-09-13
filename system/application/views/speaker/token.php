<?php
menu_pagetitle('Speaker: profile token');

// Load the sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');
?>

<div class="menu">
	<ul>
		<li><a href="/speaker/profile">Speaker Profile</a></li>
		<li><a href="/speaker/access">Profile Access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<div class="box">
    <h1>Profile token</h1>
    <table class="data-table">
        <tbody>
            <tr>
                <td class="label" style="width: 120px;">Access token</td>
                <td><?= $token->getAccessToken(); ?></td>
            </tr>
            <tr>
                <td class="label">Created</td>
                <td><?= date('m/d/Y', $token->getCreated()); ?></td>
            </tr>
            <tr>
                <td class="label">Description</td>
                <td>
                    <?= escape($token->getDescription()); ?>
                </td>
            </tr>
            <tr>
                <td class="label">Exposed fields</td>
                <td>
                    <ul>
                        <?php foreach($token->getFields() as $field) : ?>
                        <li><?= $field ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div class="bluebar right">
        <a href="/speaker/access">back to profile access</a>
    </div>
    
</div>