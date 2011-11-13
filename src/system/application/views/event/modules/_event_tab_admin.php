<div id="estats">
    <h3>Event Statistics</h3>
    <table cellpadding="0" cellspacing="0" border="0">
    <tr><td><b>Number of Sessions:</b></td><td style="padding:3px"><?php echo count($talks); ?></td></tr>
    <tr><td><b>Last Comment:</b></td><td style="padding:3px"><?php 
        echo (isset($latest_comment[0])) ? date('m.d.Y H:i:s', $latest_comment[0]->max_date) : '[none]';
    ?></td></tr>
    <tr><td><b>Total # of Comments</b></td><td style="padding:3px""><?php 
        //echo (isset($total_comment_ct)) ? $total_comment_ct : '[none]'; 
        echo $talk_stats['comments_total'];
    ?></td></tr>
    <tr><td><b>Average Session Rating</b></td><td style="padding:3px"><?php 
        echo round($talk_stats['rating_avg'],2);
    ?></td></tr>
    </table>
</div>
