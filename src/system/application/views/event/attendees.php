<?php if (count($users) == 0): ?>
<?php $this->load->view('msg_info', array('msg' => 'No attendees so far.')); ?>
<?php else: ?>
<ul>
<?php foreach ($users as $user) {
    $disp=(!empty($user->full_name)) ? $user->full_name : $user->username;
    $is_speaker=($user->is_speaker>0) ? '<span style="font-size:10px;font-weight:bold;color:#ED9D1E">speaker</span>' : '';
    ?>
    <li><a href="/user/view/<?php echo escape($user->ID); ?>"><?php echo escape($disp); ?></a> <?php echo $is_speaker; ?></li>
<?php } ?>
</ul>
<?php endif; 
//echo '<pre>'; print_r($users); echo '</pre>';
