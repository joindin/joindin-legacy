<div class="box">
    <h4><?php echo $title; ?></h4>
    <div class="ctn">
    <?php $ct=0; ?>

    <table cellpadding="10" cellspacing="0" border="0">
    <?php
    foreach ($talks as $t) {
    ?>
    <tr>
    <td valign="top" width="50%" style="padding-bottom:10px">
        <a href="/event/view/<?php echo $t->eid; ?>"><?php echo $t->event_name; ?></a><br/>
    </td>
    </tr>
    <?php } ?>
    </table>
    </div>
</div>
