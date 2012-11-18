<?php 
menu_pagetitle('Add Blog Post');
?>
<?php

$chk=array('post_mo'=>'m','post_day'=>'d','post_yr'=>'Y','post_hr'=>'H','post_mi'=>'i');
foreach ($chk as $k=>$v) {
    if (empty($this->validation->$k)) {
        $this->validation->$k=date($v);
    }
}
$sub='Submit New Post';

echo $this->validation->error_string;
if (isset($msg)) { echo '<div class="notice">'.$msg.'</div>'; }
?>

<h1>Add Blog Post</h1>
<?php 
if ($edit_id) { 
    echo form_open('blog/edit/'.$edit_id); 
} else { echo form_open('blog/add'); }
?>
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
    <td valign="top" class="title">Story:</td>
    <td><?php 
        $p=array(
            'name'	=>'story',
            'id'	=>'story',
            'cols'	=>60,
            'rows'	=>15,
            'value'	=>$this->validation->story
        );
        echo form_textarea($p); 
    ?></td>
</tr>
<tr>
    <td class="title">Post Date:</td>
    <td>
    <?php
    foreach (range(date('Y'), date('Y')+5) as $v) { $post_yr[$v]=$v; }
    foreach (range(1,24) as $v) { $post_hr[$v]=$v; }
    foreach (range(1,59) as $v) { $post_mi[$v]=$v; }
    foreach (range(1,12) as $v) { $post_mo[$v]=$v; }
    foreach (range(1,31) as $v) { $post_day[$v]=$v; }
    echo form_dropdown('post_mo', $post_mo, $this->validation->post_mo);
    echo form_dropdown('post_day', $post_day, $this->validation->post_day);
    echo form_dropdown('post_yr', $post_yr, $this->validation->post_yr);
    echo '&nbsp;@&nbsp;';
    echo form_dropdown('post_hr', $post_hr, $this->validation->post_hr);
    echo form_dropdown('post_mi', $post_mi, $this->validation->post_mi);
    ?>
    </td>
</tr>
<tr>
    <td class="title">Category:</td>
    <td>
    <?php
    $cat_list=array();
    foreach ($cats as $v) { $cat_list[$v->ID]=$v->name; }
    echo form_dropdown('category', $cat_list, $this->validation->category);
    ?>
    </td>
</tr>
<tr><td colspan="2" align="right"><?php echo form_submit('sub', $sub); ?></td></tr>
</table>
<?php echo form_close();
