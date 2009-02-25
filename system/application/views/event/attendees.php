<?php if (count($users) == 0): ?>
<?php $this->load->view('msg_info', array('msg' => 'No attendees so far.')); ?>
<?php else: ?>
<ul>
<?php foreach ($users as $user){
	$disp=(!empty($user->full_name)) ? $user->full_name : $user->username;
	?>
	<li><a href="/user/view/<?php echo escape($user->ID); ?>"><?php echo escape($disp); ?></a></li>
<?php } ?>
</ul>
<?php endif; ?>