<?php if (is_array($attend_list) && count($attend_list) > 0) {
?>
<div class="box">
    <h4>Check out who's attending!</h4>
    <div class="ctn">
    <?php
    
    foreach ($attend_list as $attendee) {
        echo '<a href="/user/view/'.$attendee->ID.'"><img src="'.$attendee->gravatar.'" height="20" width="20" style="margin:2px"i alt="'.$attendee->full_name.'" title="'.$attendee->full_name.'"/></a>';
    }
    ?>
    </div>

</div>
<?php
}
