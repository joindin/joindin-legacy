<?php menu_pagetitle($title); ?>

<h1><?= $title ?></h1>

<?= form_open($action) ?>
<?php if(isset($message) && !empty($message)) {
    $this->load->view('message/info', array('message' => $message));
}
?>
<?php if(isset($error) && !empty($error)) {
    $this->load->view('message/error', array('message' => $error));
}
?>
<div class="row">
	<label for="title">Title</label>
	<?= form_input(array(
		'name' => 'title',
		'id' => 'title',
		'size' => 30,
		'value'	=> $post->getTitle()
	)) ?>
	<div class="clear"></div>
</div>

<div class="row">
	<label for="content">Content</label>
	<?= form_textarea(array(
		'name' => 'content',
		'id' => 'content',
		'cols' => 60,
		'rows' => 15,
		'value'	=> $post->getContent()
	)) ?>
	<div class="clear"></div>
</div>

<div class="row">
	<label for="">Publish Date</label>
	<?php
	$months = $days = $years = $hours = $minutes = array();
	// fill months
	foreach(range(1, 12) as $month){ 
	    $months[$month] = $month; 
	}
	// fill days
	foreach(range(1, 31) as $day){ 
	    $days[$day] = $day; 
	}
	// fill years
	foreach(range(date('Y') -1, date('Y') + 4) as $year){ 
	    $years[$year] = $year; 
	}
	// fill hours
	foreach(range(1, 24) as $hour){ 
	    $hours[$hour] = $hour; 
	}
	// fill minutes
	foreach(range(1, 60) as $minute){ 
	    $minutes[$minute] = $minute;
	}
	
	echo form_dropdown('post_month', $months, (($post->getDate() != '') ? date('n', $post->getDate()) : date('n')));
	echo '/';
	echo form_dropdown('post_day',$days, (($post->getDate() != '') ? date('j', $post->getDate()) : date('j')));
	echo '/';
	echo form_dropdown('post_year',$years, (($post->getDate() != '') ? date('Y', $post->getDate()) : date('Y')));
	echo '&nbsp;@&nbsp;';
	echo form_dropdown('post_hour',$hours, (($post->getDate() != '') ? date('H', $post->getDate()) : date('H')));
	echo form_dropdown('post_minute',$minutes, (($post->getDate() != '') ? date('i', $post->getDate()) : date('i')));
	?>
	<div class="clear"></div>
</div>

<div class="row row-buttons">
    <?= form_submit('sub', 'Save') ?>
</div>

<?php echo form_close(); ?>
