<?php if (count($users) == 0): ?>
<?php $this->load->view('msg_info', array('msg' => 'No attendees so far.')); ?>
<?php else: ?>
<ul>
<?php foreach ($users as $user): ?>
	<li><a href="/user/view/<?php echo escape($user->ID); ?>"><?php echo escape($user->username); ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>