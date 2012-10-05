<script src="/inc/js/jquery.js"></script>
<style>
.title_link {
    font-size: 13px;
    font-weight: bold;
    text-decoration: none;
    color: #000000;
}
.rating_img {
    padding: 0px;
    margin: 0px;
    border: 0px sold #000000;
}
.loggedIn {
    background-color: #C3F214;
    color: #84A50E;
    font-weight: bold;
}
.loggedIn a {
    color: #84A50E
}
.anonymous {
    background-color: #F7C6CB;
    font-weight: bold;
    color: #F74B45;
}
</style>

<?php
/*
print_r($talk);
print_r($site);
var_dump($hasCommented);
*/
?>
<table cellpadding="2" cellspacing="0" border="0">
<tr><td colspan="2"><a href="/talk/view/<?php echo $talk->ID; ?>" class="title_link"><?php echo $talk->talk_title; ?></a></td></tr>
<tr>
    <td colspan="2"><textarea id="vote_comment" name="vote_comment" cols="25" rows="5"></textarea></td>
</tr>
<tr>
    <?php $class=(($userId)) ? 'loggedIn':'anonymous'  ?>
    <td colspan="2" style="padding:1px;font-size:11px" align="center" class="<?php echo $class; ?>">
        <?php
        if ($userId) {
            echo 'logged in as <a href="/user/view/'.$userId.'">'.$userName.'</a>';
        } else {
            echo 'commenting anonymous!';
        }
        ?>
    </td>
</tr>
<tr>
    <td>
        <?php for($i=1;$i<=5;$i++): ?>
            <a href="#" class="rating_img_link" id="r<?php echo $i; ?>">
                <img 
                    class="rating_img" 
                    src="/inc/img/rating-off.jpg" 
                    style="border:0px;margin:0px;padding:0px">
            </a>
        <?php endfor; ?>
    </td>
    <td align="right">
    <input type="button" name="btn_vote_submit" id="btn_vote_submit" value="vote"/>
    <input type="hidden" name="vote_rank" id="vote_rank"/>
</td></tr>
</table>

<script>
$('.rating_img_link').live('mouseover', function() { 
    var curr_id=this.id.replace('r',''); 
    var img_url=$('#'+this.id+' img').attr('src').replace(/rating-.+.jpg/,''); 
    for(i=1;i<=5;i++) { 
        if (i<=curr_id) { 
            $('#r'+i+' img').attr('src', img_url+'/rating-on.jpg'); 
        } else { 
            $('#r'+i+' img').attr('src', img_url+'/rating-off.jpg'); 
        } 
    } 
}); 
$('.rating_img_link').live('mouseout', function() { 
    for(i=1;i<=5;i++) { 
        var img_url=$('#'+this.id+' img').attr('src').replace(/rating-.+.jpg/,''); 
        if (!$('#vote_rank').val()) { 
            $('#r'+i+' img').attr('src', img_url+'/rating-off.jpg'); 
        } else { 
            if (i<=$('#vote_rank').val()) { 
                $('#r'+i+' img').attr('src', img_url+'/rating-on.jpg'); 
            } else { 
                $('#r'+i+' img').attr('src', img_url+'/rating-off.jpg'); 
            } 
        } 
    } 
}); 
$('.rating_img_link').live('click', function() { 
    var sel_val = this.id.replace('r',''); 
    var img_url = $('#'+this.id+' img').attr('src').replace(/rating-.+.jpg/,''); 
    $('#vote_rank').val(sel_val); 
    for(i=1;i<=sel_val;i++) { 
        $('#r'+i+' img').attr('src', img_url+'/rating-on.jpg'); 
    } 
    return false;
});

</script>
