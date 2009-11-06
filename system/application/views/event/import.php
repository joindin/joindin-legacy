<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//print_r($details);
?>

<h2>Import Event Information</h2>
<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php echo form_open_multipart('event/import/'.$details[0]->ID); ?>
<div class="row">
	<p>
	Select an XML file below to upload session information for the 
	<a href="/event/view/<?php echo $details[0]->ID; ?>"><b><?php echo $details[0]->event_name; ?></b></a>
	event. If you need more information about the format of this XML file, you can
	find it on <a href="/about/import">our "About Importing" page</a>.
	</p>
	<p>
	Your upload will be checked for correct formatting and content.
	</p>
</div>
<div class="row">
	<label for="event_end">Select file to upload:</label>
	<?php 
		$attr=array(
			'name'	=>'xml_file',
			'id'	=>'xml_file'
		);
		echo form_upload($attr); 
	?>
</div>
<div class="row">
	<?php echo form_submit('sub','Upload'); ?>
</div>

<?php echo form_close(); ?>
