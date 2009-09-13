<?php menu_pagetitle('Sessions'); ?>

<div class="box">

    <h1 class="icon-talk">Sessions</h1>
    
    <?php if(count($sessions) === 0) : ?>
    <h2 class="no-border">No sessions found!</h2>
    <p>
        No sessions were found at this moment. Please check back again soon.
    </p>
    <?php else : foreach($sessions as $session) : 
        $this->load->view('session/_session-row', array('session' => $session));
    endforeach; endif; ?>
    
</div>