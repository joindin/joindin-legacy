<?php 
menu_pagetitle('Delete Talk');
?>
<?php
if (isset($tid)) {
    echo form_open('talk/delete/'.$tid);
    ?>

    <table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            Are you sure you wish to delete this talk?<br/>
            <input type="submit" value="yes" name="answer"> 
            <input type="button" value="no" onClick="document.location='/talk/view/<?php echo $tid?>'">
        </td>
    </tr>
    </table>

    <?php 
    echo form_close(); 

} else { 
    if (isset($error) && $error == '') {
        echo 'talk removed!'; 
    } else {
        echo 'Error: ' . htmlentities($error); 
    }
}
    
