<?php
//print_r($details); print_r($comments);
$v=$details[0];

if($is_admin){
	$add='<a class="admin_link" href="/blog/edit/'.$v->ID.'">edit</a> ';
	$add.='<a class="admin_link" href="">delete</a>';
}else{ $add=''; }
echo sprintf('
	<table cellpadding="0" cellspacing="0" border="0" class="blog_post">
	<tr><td class="title"><a style="font-size:16px" href="/blog/view/%s">%s</a></td></tr>
	<tr><td class="content" style="padding:4px">%s</td></tr>
	<tr><td class="byline">%s %s %s</td></tr>
	</table>
	<br/>
',$v->ID,$v->title,nl2br($v->content),$v->author_id,date('m.d.Y H:i:s',$v->date_posted),$add);

?>
<center>
<span class="comments_title">Comments</span>
<?php
foreach($comments as $k=>$v){
	//print_r($v); echo '<br/>';
	if($is_admin){
		$add='<a class="admin_link" href="">delete</a>';
	}else{ $add=''; }
	$author=(isset($v->author_id) && $v->author_id!=0) ? 'username': 'anonymous';
	echo sprintf('
		<table cellpadding="0" cellspacing="0" border="0" class="blog_comment_tbl">
		<tr><td><span class="title">%s</span> <span class="byline">by %s %s</span></td></tr>
		<tr><td>%s</td></tr>
		</table>
	',$v->title,$author,$add,$v->content);
}
echo '<br/>';
echo '<div style="text-align:left;width:400px">'.$this->validation->error_string.'</div>';
if(isset($msg)){ echo '<div class="notice">'.$msg.'</div>'; }

echo form_open('blog/view/'.$pid);
?>
<span class="comments_title">Add a Comment</span><br/><br/>
<table cellpadding="3" cellspacing="0" border="0">
<tr>
	<td class="title">Title:</td>
	<td>
	<?php
	$p=array(
		'name'	=>'title',
		'id'	=>'title',
		'size'	=>30,
		'value'	=>$this->validation->title
	);
	echo form_input($p);
	?>
	</td>
</tr>
<tr>
	<td valign="top" class="title">Comment:</td>
	<td>
	<?php 
		$p=array(
			'name'	=>'comment',
			'id'	=>'comment',
			'cols'	=>40,
			'rows'	=>9,
			'value'	=>$this->validation->comment
		);
		echo form_textarea($p); 
	?>
	</td>
</tr>
<tr><td colspan="2" align="right"><?php echo form_submit('sub','Make comment'); ?></td></tr>
</table>
</center>
<?php echo form_close(); ?>
