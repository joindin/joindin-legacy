<?php
error_reporting(E_ALL);

menu_pagetitle('Talk: ' . escape($detail->talk_title));

if(!empty($claim_msg)){
	$class=($claim_status) ? 'notice' : 'err';
	if($claim_msg && !empty($claim_msg)){ echo '<div class="'.$class.'">'.escape($claim_msg).'</div><br/>'; }
}
?>
<script type="text/javascript" src="/inc/js/talk.js"></script>
<?php 
$msg=$this->session->flashdata('msg');
if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>
<?php
$speaker_ids= array();
$speaker    = array();

$speaker_images	= buildSpeakerImg($claim_details);
$speaker_txt	= buildClaimedLinks($speakers,$claim_details);
$rstr 			= rating_image($detail->tavg);

$data=array(
	'detail'		=> $detail,
	'speaker_txt'	=> $speaker_txt,
	'speaker_img'	=> $speaker_images,
	'rstr'			=> $rstr
);
$this->load->view('talk/modules/_talk_detail',$data);

$data=array(
	'speaker'		=> $speakers,
	'claim'			=> $claim_details
);
$this->load->view('talk/modules/_talk_buttons',$data);
?>

<p class="ad">
    <script type="text/javascript"><!--
    google_ad_client = "pub-2135094760032194";
    /* 468x60, created 11/5/08 */
    google_ad_slot = "4582459016"; google_ad_width = 468; google_ad_height = 60; //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</p>


<?php 
$data=array();
$this->load->view('talk/modules/_talk_comments',$data);
$this->load->view('talk/modules/_talk_comment_form',$data); 
?>
<input type="hidden" name="talk_id" id="talk_id" value="<?php echo $detail->ID ?>" />

<script type="text/javascript"> joindin.talk.init(); </script>
