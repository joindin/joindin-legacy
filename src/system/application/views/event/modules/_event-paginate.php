<?php
$uri_segments = $this->uri->segment_array();
?>
<b>Page:</b>
<?php for($i=1;$i<$total_count;$i++): 
    $style=($i==$current_page) ? 'style="font-weight:bold"' : '';
    ?>
    <a <?php echo $style?> href="/<?php echo $uri_segments[1]?>/<?php echo $uri_segments[2]?>/<?php echo $i?>"><?php echo $i?></a>
<?php endfor; 
