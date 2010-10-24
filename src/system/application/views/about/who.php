<?php
menu_pagetitle("Who's On Joind.in?");

echo "<h2>Who's on Joind.in?</h2>";
$rand=array_rand($users,63);
echo '<table cellpadding="0" cellspacing="0" border=0"><tr>';
$ct=1;
foreach($rand as $v){
	echo '<td style="padding:3px"><a href="/user/view/'.$users[$v].'"><img src="'.$gravatar_cache_url_fragment.'/user'.$users[$v].'.jpg" height="60"></a></td>';
	if($ct%9==0 && $ct!=0){ echo '</tr><tr>'; }
	$ct++;
}
echo '</tr></table>';
?>