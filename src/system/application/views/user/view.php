<?php
menu_pagetitle('User: ' . escape($details[0]->full_name));
if ($gravatar) { echo '<img src="'.$gravatar.'" height="80" width="80" alt="" /><br/><br/>'; } ?>
<h1><?php 
    echo (!empty($details[0]->full_name)) ? $details[0]->full_name : $details[0]->username;
?></h1>
<?php 
if ($is_admin) {
    $txt=($details[0]->active==1) ? 'Disable User Account' : 'Enable User Account'; ?>
    <a class="btn-small" href="/user/changestat/<?php echo $details[0]->ID; ?>"><?php echo $txt; ?></a>
    <?php $atxt=($details[0]->admin==1) ? 'Remove as Admin' : 'Add as Admin'; ?>
    <a class="btn-small" href="/user/changeastat/<?php echo $details[0]->ID; ?>"><?php echo $atxt; ?></a>
    <br/><br/>
<?php } ?>

<div class="box">
<?php if (count($pending_evt)>0): ?>
<h2>Your Pending Events</h2>
<?php 
foreach ($pending_evt as $e) {
    $det=$e->detail[0];
    echo '<b style="font-size:14px">'.$det->event_name.'</b><br/>'.date('m.d.Y', $det->event_start);
    if ($det->event_start+86399 != $det->event_end) echo '- '.date('m.d.Y', $det->event_end);
    echo '<br/>';
}
?>
<br/>
<?php endif; ?>

<?php
if (!empty($details[0]->twitter_username)) {
    echo '<a href="https://twitter.com/'.$details[0]->twitter_username.'">@'.$details[0]->twitter_username.'</a><br/><br/>';
}

$uid=$details[0]->ID;

if (!isset($sort_type)) { $sort_type = 'all'; }
switch ($sort_type) {
    case 'lastcomment':
        $talk_cdate = array();
        $tmp_talk   = array();
        foreach ($talks as $k=>$v) {
            $talk_cdate[$v->ID] = $v->last_comment_date;
            $tmp_talk[$v->ID]   = $v;
        }
        arsort($talk_cdate);
        // Resort our talks
        $tmp=array();
        foreach ($talk_cdate as $k=>$v) { $tmp[]=$tmp_talk[$k]; }
        $talks=$tmp;
        $title = 'Talks (By Latest Comment)'; break;
    case 'bycomment':
        $talk_ccount    = array();
        $tmp_talk   = array();
        foreach ($talks as $k=>$v) {
            $talk_ccount[$v->ID]= $v->ccount;
            $tmp_talk[$v->ID]   = $v;
        }
        arsort($talk_ccount);
        foreach ($talk_ccount as $k=>$v) { $tmp[]=$tmp_talk[$k]; }
        $talks=$tmp;
        $title = 'Talks (By Comment Count)'; break;
    case 'byname':
        // group them together by name - down below it'll look for 
        // the subarray
        $talks_by_name = array();
        foreach ($talks as $talk) {
            $talks_by_name[trim($talk->talk_title)][]=$talk;
        }
        ksort($talks_by_name);
        $title = 'Talks (By Name)'; break;
    default:
        $title = 'Talks'; break;
}
?>
<h2><?php echo $title; ?></h2>

<?php if (count($talks) == 0): ?>
    <p>No talks so far</p>
<?php else: ?>
    <p class="filter">
        <a href="/user/view/<?php echo $uid; ?>">Date Presented</a> |
        <a href="/user/view/<?php echo $uid; ?>/lastcomment">Last Commented</a> |
        <a href="/user/view/<?php echo $uid; ?>/bycomment">By Comment</a> |
        <a href="/user/view/<?php echo $uid; ?>/byname">By Name</a>
    </p>
    <?php
        if ($sort_type=='byname') {
            foreach ($talks_by_name as $talk_title => $talks) {
                echo '<h3>'.$talk_title.'</h3><br/>';
                echo '<div style="padding-left:15px">';
                foreach ($talks as $talk) {
                    $this->load->view('talk/_talk-row', array(
                        'talk'      => $talk,
                        'override'  => array($details[0]->ID=>$details[0]->full_name)
                    ));
                }
                echo '</div>';
            }
        } else {
            foreach ($talks as $talk) {
                $this->load->view('talk/_talk-row', array(
                    'talk'      => $talk,
                    'override'  => array($details[0]->ID=>$details[0]->full_name)
                ));
            }
        }
    ?>
<?php endif; ?>
</div>

<div class="box">
    <h2>Comments</h2>
    <div class="UserViewCommentDetailsControl" onclick="$('.UserViewCommentDetails').css('display', 'block'); $('.UserViewCommentDetailsControl').css('display', 'none');">(Show Details)</div>
    <div class="UserViewCommentDetails" onclick="$('.UserViewCommentDetails').css('display', 'none'); $('.UserViewCommentDetailsControl').css('display', 'block');">(Hide Details)</div>
<?php if (count($comments) == 0): ?>
    <p>No comments so far</p>
<?php else: ?>
    <?php foreach ($comments as $k=>$v):
        if ($v->private && user_get_id() != $details[0]->ID) {
            continue;
        }
    ?>
    <div class="row">
        <?php echo rating_image($v->rating, "small");?>&nbsp;<div class="UserViewCommentDetails">(<?php echo date('d.M.Y', $v->date_made)?>)</div><strong><a href="/talk/view/<?php echo $v->talk_id; ?>#comment-<?php echo $v->ID; ?>"><?php echo escape($v->talk_title); ?></a></strong>
        <?php if ($v->private) { echo ' (private)'; } ?>
        <div class="clear UserViewCommentDetails"><?php echo escape($v->comment) ?></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<?php
//sort the events
$ev=array('attended'=>array(),'attending'=>array());
foreach ($is_attending as $k=>$v) {
    if ($v->event_end<time()) {
        $ev['attended'][]=$v; 
    } else { $ev['attending'][]=$v; }
}
//minimize my attending
$my=array();
foreach ($my_attend as $k=>$v) { $my[]=$v->ID; }

//check the date and, if they have talks in their list, be sure that its in the list
foreach ($talks as $k=>$v) {
    $d=array(
        'event_name'    => $v->event_name,
        'event_start'   => $v->date_given,
        'ID'            => $v->eid
    );
    $d=(object)$d;
    if ($v->date_given<time()) {
        $ev['attended'][]=$d;
    } else { $ev['attending'][]=$d; }
}
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td>
        <div class="box">
            <h2>Events They'll Be At</h2>
        <?php if (count($ev['attending']) == 0): ?>
            <p>No events so far</p>
        <?php else: ?>
            <?php 
            $eids=array();
            foreach ($ev['attending'] as $k=>$v) { 
                if (in_array($v->ID, $eids)) { continue; } else { $eids[]=$v->ID; }
            ?>
            <div class="row">
                <strong><a href="/event/view/<?php echo $v->ID; ?>"><?php echo escape($v->event_name); ?></a></strong>
                <?php echo date('M d, Y', $v->event_start); ?>
                <?php if (in_array($v->ID, $my)) { echo "<br/><span style=\"color:#92C53E;\">you'll be there!</span>"; } ?>
                <div class="clear"></div>
            </div>
            <?php } ?>
        <?php endif; ?>
        </div>
    </td>
    <td>
        <div class="box">
            <h2>Events They Were At</h2>
        <?php if (count($ev['attended']) == 0): ?>
            <p>No events so far</p>
        <?php else: ?>
            <?php 
            $eids=array();
            foreach ($ev['attended'] as $k=>$v) {
                if (in_array($v->ID, $eids)) { continue; } else { $eids[]=$v->ID; }
            ?>
            <div class="row">
                <strong><a href="/event/view/<?php echo $v->ID; ?>"><?php echo escape($v->event_name); ?></a></strong>
                <?php echo date('M d, Y', $v->event_start); ?>
                <?php if (in_array($v->ID, $my)) { echo "<br/><span style=\"color:#92C53E\">you were there!</span>"; } ?>
                <div class="clear"></div>
            </div>
            <?php } ?>
        <?php endif; ?>
        </div>
    </td>
</tr>
<?php if ($is_admin) { ?>
<tr>
    <td colspan="2">
        <div class="box">
            <h2>Admin</h2>

            <?php if (!empty($uadmin['events'])) : ?>
            <p>Events for which they are an admin</p>
            <table cellpadding="3" cellspacing="0" border="0">
            <?php
            foreach ($uadmin['events'] as $k=>$v) {
                if (!isset($v->detail[0])) { continue; }
                $title=escape($v->detail[0]->event_name);
                $url='/event/view/'.$v->detail[0]->ID;
                $pend=($v->rcode=='pending') ? ' (pending)':'';
                echo sprintf('
                    <tr id="resource_row_%s">
                        <td style="padding:3px"><a href="%s">%s %s</a></td>
                        <td style="padding:3px">[<a href="#" onClick="removeRole(%s);return false;" title="Remove this user from this event">X</a>]</td>
                    </tr>
                ', $v->admin_id, $url, $title, $pend, $v->admin_id);
            }
            ?>
            </table>
            <?php endif; ?>

            <?php if (!empty($uadmin['pending_talks'])) : ?>
            <p>Pending talk claims</p>
            <table cellpadding="3" cellspacing="0" border="0">
            <?php
            $count = 0;
            foreach ($uadmin['pending_talks'] as $k=>$v) {
                $count++;
                $row_id = 'p'. $count;
                $url = '/talk/view/'.$v->talk_id;
                $title = $v->talk_title;
                $event_url = '/event/view/'.$v->event_id;
                $event_name = $v->event_name;
                $event_date = date('M d, Y', $v->event_start);
                echo sprintf('
                    <tr id="resource_row_%s">
                        <td style="padding:3px">
                            <a href="%s">%s</a>
                            at <a href="%s">%s</a> %s
                        </td>
                        <td style="padding:3px">[<a href="javascript:" onClick="removeTalkClaim(%s, \'%s\');return false;" title="Remove this speaker\'s claim">X</a>]</td>
                    </tr>
                ', $row_id, $url, $title, $event_url, $event_name, $event_date, $v->ID, $row_id);
            }
            ?>
            </table>
            <?php endif; ?>

            <?php if (!empty($uadmin['talks'])) : ?>
            <p>Claimed talks</p>
            <table cellpadding="3" cellspacing="0" border="0">
            <?php
            $count = 0;
            foreach ($uadmin['talks'] as $k=>$v) {
                $count++;
                $row_id = 't'. $count;
                $title=escape($v->talk_title);
                $url='/talk/view/'.$v->ID;
                $event_url = '/event/view/'.$v->event_id;
                $event_name = $v->event_name;
                $event_date = date('M d, Y', $v->event_start);
                echo sprintf('
                    <tr id="resource_row_%s">
                        <td style="padding:3px">
                            <a href="%s">%s</a>
                            at <a href="%s">%s</a> %s
                        </td>
                        <td style="padding:3px">[<a href="javascript:" onClick="unlinkSpeaker(%s, %s, \'%s\');return false;" title="Unlink this speaker">X</a>]</td>
                    </tr>
                ', $row_id, $url, $title, $event_url, escape($event_name),
                $event_date, $v->ID, $v->speaker_id, $row_id);
            }

            ?>
            </table>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php } ?>
</table>
