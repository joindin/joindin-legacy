<?php
ob_start();
echo form_open('/claim/token');
echo form_input(array('name' => 'claim_token', 'style' => 'width:95%'));
echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Submit');
echo form_close();
?>
<p>
    Enter your session code above to claim your session and have access to private comments from visitors. <a href="/about/contact">Contact Us</a> to have the code for your talk sent via email.
</p>

<?php menu_sidebar('Claim a session', ob_get_clean()); ?>
