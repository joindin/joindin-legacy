<script type="text/javascript" src="/inc/js/talk.js"></script>
<?php
$event_list	= array(); 
$cat_list	= array();
$lang_list	= array();

//echo '<pre>'; print_r($cats); echo '</pre>';
//echo '<pre>'; print_r($tracks); echo '</pre>';

$ev=$events[0];
foreach ($cats as $k=>$v) { $cat_list[$v->ID]=$v->cat_title; }
foreach ($langs as $k=>$v) { $lang_list[$v->ID]=$v->lang_name; }

if (!empty($this->validation->error_string)) {
    $this->load->view('msg_info', array('msg' => $this->validation->error_string));
}

if (isset($this->edit_id)) {
    $actionUrl = 'talk/edit/'.$this->edit_id;
    $sub	= 'Save Edits';
    $title	= 'Edit Session: '.$detail[0]->talk_title;
    menu_pagetitle('Edit Session: '.$detail[0]->talk_title);
} else { 
    $actionUrl =  'talk/add/event/'.$ev->ID;
    $sub	= 'Add Session';
    $title	= 'Add Session';
    menu_pagetitle('Add Session');
}
echo '<h2>'.$title.'</h2>';

if (isset($msg) && !empty($msg)) { $this->load->view('msg_info', array('msg' => $msg)); }
if (isset($err) && !empty($err)) { $this->load->view('msg_info', array('msg' => $err)); }
$priv=($evt_priv===true) ? ', Private Event' : '';
?>

<?php echo form_open($actionUrl); ?>

<div id="box">
    
    <div class="row">
    <label for="event"></label>
    <?php
    echo form_hidden('event_id', $ev->ID);
    echo '<b><a href="/event/view/'.$ev->ID.'">'.escape($ev->event_name).'</a> ('.date('d.M.Y', $ev->event_start);
    if ($ev->event_start+86399 != $ev->event_end) echo '- '.date('d.M.Y', $ev->event_end);
    echo ')'.$priv.'</b>';
    ?>
    <div class="clear"></div>
    </div>
    <div class="row">
    <label for="talk_title">Session Title</label>
    <?php echo form_input('talk_title', $this->validation->talk_title);?>
    <div class="clear"></div>
    </div>
    <div class="row">
    <label for="speaker">Speaker</label>

    <span style="color:#3567AC;font-size:11px">
        One speaker per row, add more rows for more than one speaker.<br/>
        To <b>remove</b> a speaker, remove their name from the text field and submit.
    </span>
    <?php
    
    // if editing and already have speakers...
    if (isset($this->validation->speaker) && count($this->validation->speaker) != 0) {
        foreach ($this->validation->speaker as $speakerId => $speaker) {
            echo form_input('speaker_row['.$speakerId.']', $speaker->speaker_name);
        }
    } else {
        echo form_input('speaker_row[new_1]','');
    }
    ?>
    <div id="speaker_row_container">
        
    </div>
    <?php 
    $attr=array(
        'name'	=> 'add_speaker_line',
        'id'	=> 'add_speaker_line',
        'value'	=> '+ more',
        'type'	=> 'button'
    );
    echo form_input($attr);
    ?>
    <noscript>
    <!-- no javascript? no problem... -->
    <?php echo form_input('speaker_row[new_1]'); ?>
    </noscript>
    <div class="clear"></div>
    
    </div>
    <div class="row">
    <label for="session_date">Date and Time of Session</label>
    <?php
    /*foreach (range(1,12) as $v) {
        $m=date('M', mktime(0,0,0, $v,1, date('Y')));
        $given_mo[$v]=$m; }
    foreach (range(1,32) as $v) { $given_day[$v]=$v; }
    foreach (range(2007, date('Y')+5) as $v) { $given_yr[$v]=$v; }
    echo form_dropdown('given_mo', $given_mo, $this->validation->given_mo);
    echo form_dropdown('given_day', $given_day, $this->validation->given_day);
    echo form_dropdown('given_yr', $given_yr, $this->validation->given_yr);*/
    $eventStart = $this->timezone->getDatetimeFromUnixtime($thisTalksEvent->event_start, $thisTalksEvent->timezoneString);
    $eventEnd = $this->timezone->getDatetimeFromUnixtime($thisTalksEvent->event_end, $thisTalksEvent->timezoneString);
    $listData = array();
    
    $eventSelected = $eventStart->format('U'); // modify for existing date
    while ($eventStart->format('U') <= $eventEnd->format('U')) {
        $listData[$eventStart->format('Y-m-d')] = $eventStart->format('jS M Y');
        $eventStart->modify('+1 day');
    }
    $talkDate = (!isset($this->validation->talkDate)) ? $eventSelected : $this->validation->talkDate;

    echo form_dropdown('talkDate', $listData, $talkDate), ' at ';
    foreach (range(0,23) as $v) { $given_hour[$v]=str_pad($v,2,'0', STR_PAD_LEFT); }
    foreach (range(0,55, 5) as $v) { $given_min[$v]=str_pad($v,2,'0', STR_PAD_LEFT); }
    echo form_dropdown('given_hour', $given_hour, $this->validation->given_hour);
    echo form_dropdown('given_min', $given_min, $this->validation->given_min);
    ?>
    <div class="clear"></div>
    </div>
    <div class="row">
    <label for="session_type">Session Type</label>
    <?php
        $stype			= null;
        $sessionType 	= null;
        if (isset($this->validation->session_type)) {
            foreach ($cat_list as $categoryId => $categoryName) {
                if ($categoryName==$this->validation->session_type) { $sessionType=$categoryId; }
            }
        } else { $sessionType=$this->validation->session_type; }
        echo form_dropdown('session_type', $cat_list, $sessionType); 
    ?>
    <div class="clear"></div>
    </div>

    <?php if (!empty($tracks)): ?>
    <div class="row">
    <label for="session_track">Session Track</label>
    <?php
    $tarr=array('none'=>'No track');
    foreach ($tracks as $track) { $tarr[$track->ID]=$track->track_name; }
    echo form_dropdown('session_track', $tarr, $this->validation->session_track); 
    ?>
    <div class="clear"></div>
    </div>
    <?php endif; ?>

    <div class="row">
    <label for="session_lang">Session Language</label>
    <?php
        $slang 		= null;
        $useDefault = (empty($this->validation->session_lang)) ? 'English - US' : null;
        
        if (isset($this->validation->session_lang)) {
            foreach ($lang_list as $langId => $langText) {
                if ($langId == $this->validation->session_lang) { 
                    $slang = $langId; 
                } else {
                    //see if we should use our default & if this is it
                    if ($useDefault != null && $langText == $useDefault) {
                        $slang = $langId;
                    }
                }
            }
        } else { $slang = $this->validation->session_lang; }
        echo form_dropdown('session_lang', $lang_list, $slang); 
    ?>
    <div class="clear"></div>
    </div>
    <div class="row">
    <label for="session_desc">Session Description</label>
    <?php
    $arr=array(
        'name'=>'talk_desc',
        'value'=>$this->validation->talk_desc,
        'cols'=>40,
        'rows'=>10
    );
    echo form_textarea($arr);
    ?>
    <div class="clear"></div>
    </div>

    <div class="row">
    <label for="slides_link">Slides Link</label>
    <td><?php echo form_input('slides_link', $this->validation->slides_link); ?></td>
    <div class="clear"></div>
    </div>
    <div class="row">
    <?php echo form_submit('sub', $sub); ?>
    </div>
</div>

<?php form_close(); ?>

<script type="text/javascript">
$('#add_speaker_line').css('display','block');
$(document).ready(function() {
    talk.init();
})
</script>

