
<img src="/inc/img/search.gif"/>
<br/><br/>
<?php
echo $this->validation->error_string;

//echo '<pre>'; print_r($results); echo '</pre>';

echo form_open('/search');
?>
<center>
<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td><b>SEARCH:</b></td>
	<td>
		<?php 
		$arr=array(
			'name'	=> 'search_term',
			'id'	=> 'search_term',
			'size'	=> 50,
			'value'	=> $this->validation->search_term
		);
		echo form_input($arr); 
		?>
	</td>
</tr>
<tr>
	<td colspan="2" align="right">
		<?php
		foreach(range(1,12) as $v){ $start_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $start_day[$v]=$v; }
		foreach(range(date('Y')-5,date('Y')+5) as $v){ $start_yr[$v]=$v; }
		
		$start_mo	= array_merge(array(''=>''),$start_mo);
		$start_day	= array_merge(array(''=>''),$start_day);
		$start_yr	= array_merge(array(''=>''),$start_yr);
		
		echo form_dropdown('start_mo',$start_mo,$this->validation->start_mo);
		echo form_dropdown('start_day',$start_day,$this->validation->start_day);
		echo form_dropdown('start_yr',$start_yr,$this->validation->start_yr);
		echo ' - ';
		echo form_dropdown('end_mo',$start_mo,$this->validation->end_mo);
		echo form_dropdown('end_day',$start_day,$this->validation->end_day);
		echo form_dropdown('end_yr',$start_yr,$this->validation->end_yr);
		?>
	</td>
</tr>
<tr>
	<td align="right" colspan="2"><?php echo form_submit('sub','search'); ?></td>
</tr>
</table>
</center>
<?php 
echo form_close(); 

if(!empty($results)){
	echo '<img src="/inc/img/sr_events.gif"/><br/><br/>';
	if(isset($results['events'])){
		foreach($results['events'] as $k=>$v){
			$str='';
			$tmp=explode(' ',$v->event_desc);
			for($i=0;$i<=20;$i++){ if(isset($tmp[$i])){ $str.=$tmp[$i].' '; } }
			echo sprintf('
				<a href="/event/view/%s">%s</a><br/>
				%s<br/>
			',$v->ID,$v->event_name,trim($str).'...');
		}
	}
	echo '<hr/><br/>';
	echo '<img src="/inc/img/sr_talks.gif"/><br/><br/>';
	if(isset($results['talks'])){
		foreach($results['talks'] as $k=>$v){
			$str='';
			$tmp=explode(' ',$v->talk_desc);
			for($i=0;$i<=20;$i++){ $str.=$tmp[$i].' '; }
			echo sprintf('
				<a href="/talk/view/%s">%s</a><br/>
				<div style="padding-left:5px;padding-bottom:4px">%s</div>
			',$v->ID,$v->talk_title,trim($str).'...');
		}
	}
}
?>

