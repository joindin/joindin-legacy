<div class="box">
	<h4><?= $title ?></h4>
	<div class="ctn">
		<p>
		    Here's a few of the other popular posts to our blog:
		</p>
		<ul>
    	<?php 
			foreach($posts as $post){
				echo '<li><a href="/blog/view/' . $post->getId() . '">' . $post->getTitle() . '</a>';
			}
		?>
		</ul>
	</div>
</div>
