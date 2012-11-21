<?php 
menu_pagetitle('Unlink Talk Claim');
?>
<?php
if (isset($talkId)) {
    echo form_open('talk/unlink/'.$talkId.'/'.$speakerId);
    ?>

    <table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            Are you sure you wish to unlink this talk claim?<br/>
            <input type="submit" value="yes" name="answer"> 
            <input type="button" value="no" onClick="document.location='/talk/view/<?php echo $talkId?>'">
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
    
