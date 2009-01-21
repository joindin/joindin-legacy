<center>
	<a href="/user/manage">Manage Account</a> ||
	<?php if($is_admin){ echo '<a href="/user/admin">User Admin</a>'; } ?>
</center>

<br/>
<?php
$fmsg=$this->session->flashdata('msg');
if(isset($msg) && !empty($msg)){ 
	echo '<div class="notice">'.$msg.'</div><br/>';
}elseif(!empty($fmsg)){
	echo '<div class="notice">'.$fmsg.'</div><br/>';	
}
?>
<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td valign="top" width="50%">
	<img src="/inc/img/my_comments.gif" style="margin-bottom:9px"/>
	<table cellpadding="3" cellspacing="0" border="0">
		<?php
		foreach($comments as $k=>$v){
			echo '<tr><td valign="top">';
			for($i=1;$i<=$v->rating;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; } echo '</td>';
			echo '<td><a href="/talk/view/'.$v->talk_id.'#'.$v->ID.'">'.$v->talk_title.'</a><br/>';
			echo '<span style="font-size:10px;color:#A6A6A6">made '.date('m.d.Y',$v->date_made).'</span></td></tr>';
		}
		//print_r($comments);
		?>
		</table>
		<br/>
	</td>
	<td valign="top" width="50%">
		<img src="/inc/img/claim_talk.gif" style="margin-bottom:9px"/>
		<?php
		if(!empty($this->validation->error_string)){
			echo $this->validation->error_string.'<br/>';
		}
		echo form_open('user/main');
		echo form_input('talk_code');
		echo form_submit('sub','Submit');
		form_close();
		?>
		<p>
		<span style="font-size:10px;color:#A6A6A6">Enter your talk code above to claim your talk and have access to private comments from visitors. <a href="/about/contact">Contact Us</a> to have the code for your talk sent via email.</span>
		</p>
		<br/>
		<img src="/inc/img/my_talks.gif" style="margin-bottom:9px"/>
		<table cellpadding="3" cellspacing="0" border="0">
		<?php
		if(!empty($talks)){
			foreach($talks as $k=>$v){
				echo '<tr>';
				echo '<td valign="top">';
				for($i=1;$i<=$v->tavg;$i++){ echo '<img src="/inc/img/thumbs_up.jpg" height="20"/>'; }
				echo '</td>';
				echo '<td><a style="font-size:12px" href="/talk/view/'.$v->tid.'">'.$v->talk_title.'</a> <br/><span style="font-size:10px;color:#A6A6A6">('.$v->event_name.' - '.date('m.d.Y',$v->date_given).')</span></td>';
				echo '</tr>';
			}
		}else{ echo 'No current talks...'; }
		?>
		</table>
	</td>
</tr>
</table>