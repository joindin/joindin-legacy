<?php
$admin=false;
?>
<img src="/inc/img/current_events.gif"/>

<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr>
	<td>
		<?php /*echo $this->calendar->generate($this->uri->segment(3), $this->uri->segment(4));*/ ?>
		<?php
		$mo=date('m');
		$yr=date('Y');
		
		$day=1;
		$start_dow=date('N',mktime(0,0,0,$mo,1,$yr));
		?>
	</td>
</tr>
<tr>
	<td valign="top">
		<?php
		foreach($events as $k=>$v){
			echo '<a style="font-size:13px;font-weight:bold;" href="/event/view/'.$v->ID.'">'.$v->event_name.'</a><br/><div style="padding-left:8px;padding-top:5px">'.$v->event_desc.'<br/>';
			echo '<span style="color:#A2A2A2">'.date('m.d.Y',$v->event_start).'-'.date('m.d.Y',$v->event_end).'</span><br/>';
			echo '</div><br/>';
		}
		?>
	</td>
</tr>
</table>

<?php if($admin){ ?>
<a href="/event/add">add new event</a>
<?php } ?>