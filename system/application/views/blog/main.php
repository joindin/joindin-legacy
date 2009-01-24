<?php
//echo '<pre>'; print_r($posts); echo '</pre>';

if(count($posts)>0){
	foreach($posts as $k=>$v){
		if($is_admin){
			$add='<a class="admin_link" href="/blog/edit/'.$v->ID.'">edit</a> ';
			$add.='<a class="admin_link" href="">delete</a>';
		}else{ $add=''; }
		$com=($v->comment_count!=1) ? $v->comment_count.' comments' : $v->comment_count.' comment';
		echo sprintf('
			<table cellpadding="0" cellspacing="0" border="0" class="blog_post">
			<tr><td class="title"><a href="/blog/view/%s">%s</a></td></tr>
			<tr><td class="content">%s</td></tr>
			<tr>
				<td class="byline">
					<a class="comments_link" href="/blog/view/%s">%s</a>
					%s %s %s<br/>
				</td>
			</tr>
			</table>
			<br/>
		',$v->ID,$v->title,nl2br($v->content),$v->ID,$com,$v->author_id,
		date('m.d.Y H:i:s',$v->date_posted),$add);
	}
}else{
	echo 'No posts yet! Come back soon!';
}
?>