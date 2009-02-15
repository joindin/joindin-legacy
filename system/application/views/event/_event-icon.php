<?php 
	$path=$_SERVER['DOCUMENT_ROOT'].'/inc/img/event_icons/';
	$img=(!empty($img) && is_file($path.$img)) ? $img : 'none.gif'; 
?>
<img src="/inc/img/event_icons/<?php echo $img ?>"/>