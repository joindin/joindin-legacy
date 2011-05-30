<?php

menu_pagetitle('Manage Speaker Profile Access');

$this->load->view('user/_nav_sidebar');
?>
<h2>Delete Token Access</h2>
<?php echo form_open('speaker/access/delete/'.$tid); ?>

<div id="box">
	<div class="row">
		Are you sure you wish to delete this token and associated profile access?<br/>
		<?php echo form_submit('sub','Yes'); ?>
		<?php echo form_submit('sub','No'); ?>
		<div claass="clear"></div>
	</div>
</div>

<?php echo form_close(); ?>
