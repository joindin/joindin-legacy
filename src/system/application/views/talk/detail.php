<?php
error_reporting(E_ALL);

menu_pagetitle('Talk: ' . escape($detail->talk_title));

if(!empty($claim_msg)){
	$class=($claim_status) ? 'notice' : 'err';
	if($claim_msg && !empty($claim_msg)){ echo '<div class="'.$class.'">'.escape($claim_msg).'</div><br/>'; }
}
?>
<?php 
$msg=$this->session->flashdata('msg');
if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>
<?php
$speaker_ids= array();
$speaker    = array();

if(empty($speaker_claim)){ $speaker[]=escape($detail->speaker); }
$speaker_txt= implode(', ',$speaker);
$rstr 		= rating_image($detail->tavg);

$data=array(
	'detail'		=> $detail,
	'speaker_txt'	=> $speaker_txt,
	'rstr'			=> $rstr
);
$this->load->view('talk/modules/_talk_detail',$data);

$data=array(
	'speaker'		=> $speaker
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


