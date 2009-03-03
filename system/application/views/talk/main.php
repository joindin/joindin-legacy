<?php 
menu_pagetitle('Talks');
?>
<?php
//echo '<pre>'; print_r($talks); echo '</pre>';
?>
<h1 class="icon-talk">Talks</h1>

<?php
foreach(array_slice($talks,0,10) as $v){
	$this->load->view('talk/_talk-row', array('talk'=>$v));
	
}
?>