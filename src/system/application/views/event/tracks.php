<?php
$evt_detail=$detail[0];
//echo '<pre>'; print_r($tracks); echo '</pre>';
?>

<style>
#track_tbl { width: 100%; }
#track_tbl td { padding: 3px; border: 0px solid #000000; }
#track_tbl tr.header td { font-weight: bold; background-color: #F2F4F8; color: #28569C; }
#track_tbl tr.row1 { background-color: #EEEEEE; }
#track_tbl tr.row2 { background-color: #FFFFFF; }
div.track_color {
    width: 9px;
    height: 9px;
    border: 1px solid #000000;
}
</style>

<h2>Tracks for <?php echo escape($evt_detail->event_name); ?></h2>
<a href="/event/view/<?php echo $evt_detail->ID; ?>">Return to event</a>
<br/><br/>
<?php $ct=0; ?>
<div id="box">
<table cellpadding="0" cellspacing="0" border="0" id="track_tbl">
<tr class="header">
    <td>&nbsp;</td>
    <td>Title</td>
    <td>Description</td>
    <td>In use</td>
    <td>&nbsp;</td>
</tr>
<tbody id="track_tbl_body">
<?php foreach ($tracks as $tr): ?>
<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>" id="rid_<?php echo $ct; ?>" data-trackid="<?php echo $tr->ID; ?>">
    <?php $color=(!empty($tr->track_color)) ? 'style="background-color:#'.$tr->track_color.'"' : ''; ?>
    <td style="vertical-align:top">
        <!--
        <div class="track_color" id="track_color_<?php echo $ct; ?>_block" <?php echo $color; ?>></div>
        <input type="hidden" name="track_color_<?php echo $ct; ?>" id="track_color_<?php echo $ct; ?>" value="<?php echo $color; ?>"/>
        <div style="display:none;margin-top:2px" id="track_color_sel_<?php echo $ct; ?>">
            <?php
            $color_arr=array('FF0000','FFFF00','0000FF');
            foreach ($color_arr as $c) {
                echo '<a href="#" onClick="updateTrackColor('.$ct.',\''.$c.'\')" <div class="track_color" style="background-color:#'.$c.';margin-bottom:2px"></div></a>';
            }
            ?>
        </div>
        -->
    </td>
    <td style="vertical-align:top">
        <div id="disp_name_<?php echo $ct; ?>"><?php echo $tr->track_name; ?></div>
        <input type="text" size="15" name="name_<?php echo $ct; ?>" id="name_<?php echo $ct; ?>" value="<?php echo $tr->track_name;?>" style="display:none"/>
        <!-- track ID -->
        <input type="hidden" name="trackid_<?php echo $ct; ?>" id="trackid_<?php echo $ct; ?>" value="<?php echo $tr->ID; ?>"/>
    </td>
    <td style="vertical-align:top">
        <div id="disp_desc_<?php echo $ct; ?>"><?php echo $tr->track_desc; ?></div>
        <textarea cols="25" rows="4" name="desc_<?php echo $ct; ?>" id="desc_<?php echo $ct; ?>" style="display:none"><?php echo $tr->track_desc; ?></textarea>
    </td>
    <td style="vertical-align:top"><?php echo $tr->used; ?></td>
    <td align="right" id="ctrl_cell_<?php echo $ct; ?>" style="vertical-align:top">
        <a href="#" class="btn-small" onClick="editTrack(<?php echo $ct; ?>)">edit</a>
        <a href="#" class="btn-small" onClick="deleteTrack('<?php echo $ct."','".$tr->ID; ?>')">delete</a>
    </td>
</tr>
<?php $ct++; endforeach; ?>
</tbody>
<tr>
    <td align="right" colspan="5"><a href="#" onClick="addTrackRow()" class="btn-small btn-success">Add a New Track</a></td>
</tr>
</table>
</div>
<input type="hidden" name="event_id" id="event_id" value="<?php echo $evt_detail->ID; ?>"/>

<!--
<div id="add_track_form">
<?php if ($admin): echo '<br/>';
echo form_open('event/track/'.$evt_detail->ID);
?>
<div id="box">
    <h2>Track Details</h2>
    <div class="row">
    <label for="track_name">Track Name</label>
    <?php echo form_input('track_name'); ?>
    <div class="clear"></div>
    </div>
        
    <div class="row">
    <label for="track_name">Track Name</label>
    <?php 
    $attr=array(
        'name'=>'track_desc',
        'id'=>'track_desc',
        'cols'=>8,
        'rows'=>5
    );
    echo form_textarea($attr); ?>
    <div class="clear"></div>
    </div>
    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-small'), 'Submit Track Data'); ?>
    </div>
</div>
<?php echo form_close(); ?>
<?php endif; ?>
</div>
-->
