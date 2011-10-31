<?php 
//print_r($talks); 
$ct=0; ?>

<table cellpadding="10" cellspacing="0" border="0"><tr>
<?php 
foreach ($talks as $t) { 
    if ($ct%2==0 && $ct!=0) { echo '</tr><tr>'; } 
?>
<td valign="top" width="50%" style="padding-bottom:10px">
    <div>
    <div class="text">
        <h3><a href="/talk/view/<?php echo escape($t->ID); ?>"><?php echo escape($t->talk_title); ?></a></h3>
        <?php echo $t->speaker; ?>
        <div class="img" style="padding-top:5px"><?php echo rating_image($t->rating); ?></div>
    </div>
    </div>
</td>
<?php $ct++; } ?>
</tr></table>
