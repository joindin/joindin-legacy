<?php 
//echo '<pre>'; print_r($user); echo '</pre>';

$uname=(!empty($user->full_name)) ? $user->full_name : $user->username;
?>
<div class="row">
    <div class="text">
        <h3><a href="/user/view/<?php echo escape($user->ID); ?>"><?php echo escape($uname); ?></a></h3>
        <p class="opts">
        <?php echo escape($user->talk_count); ?> talks | <?php echo escape($user->talk_count+$user->event_count); ?> events
        </p>
    </div>
    <div class="clear"></div>
</div>
