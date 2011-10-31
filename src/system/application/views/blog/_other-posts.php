<?php
//print_r($posts);
$ct		= 0;
$limit	= 10;
?>
<div class="box">
    <h4><?php echo $title; ?></h4>
    <div class="ctn">
        <p>
        Here's a few of the other popular posts to our blog:
        </p>
        <ul>
        <?php 
            //print_r($content);
            foreach ($posts as $k=>$v) {
                echo '<li><a href="/blog/view/'.$v->ID.'">'.$v->title.'</a>';
            }
        ?>
        </ul>
    </div>
</div>
