<?php
if (isset($eid)) {
    echo '<h1 class="title">Delete event '.escape($details[0]->event_name).' ?</h1>';
    
    echo form_open('event/delete/'.$eid);
    ?>

    <table cellpadding="3" cellspacing="0" border="0">
    <tr>
        <td>
            Are you sure you wish to delete this event?<br/>
            <input type="submit" value="yes" name="answer"> 
            <input type="submit" value="no" name="answer">
        </td>
    </tr>
    </table>

    <?php 
    echo form_close(); 

} else {
    echo '<h1 class="title">Event Removed!</h1>';
    echo '<a href="/event/pending">Return to pending event list</a>';
}
