<?php
// Load some sidebars
$this->load->view('sidebar/user-navigation');
$this->load->view('sidebar/claim-session');

// Catch Flash message
$this->load->view('message/flash');

// Add message area
$this->load->view('message/area');
?>

<div class="menu">
	<ul>
		<li class="active"><a href="/speaker/talks">My Talks</a></li>
		<li><a href="/talk/access">Talk Access</a></li>
	</ul>
	<div class="clear"></div>
</div>

<h2 class="h1">
    My Talks
    <a href="/talk/add" class="btn btn-green" style="position: absolute; right: 0; margin-top: 4px;">Add Talk</a>
</h2>

<?php foreach($talks as $talk) : ?>
<div class="box detail">
    <h2><a href="/talk/view/<?= $talk->getId() ?>"><?= escape($talk->getTitle()) ?></a></h2>

    <div class="talk-details" id="talk-<?= $talk->getId() ?>">
        <a name="talk-<?= $talk->getId() ?>"></a>
        <p>
            <?= escape($talk->getDescription()) ?>
        </p>
        <div style="border-top: 1px dotted #D7DCDF; text-align: right; padding: 4px 4px 0 0;">
            <a href="/talk/view/<?= $talk->getId() ?>">view</a>&nbsp;-&nbsp;
            <a href="/talk/edit/<?= $talk->getId() ?>">edit</a>&nbsp;-&nbsp;
            <a href="/talk/delete/<?= $talk->getId() ?>">delete</a>
        </div>
    </div>
</div>
<?php endforeach; ?>

