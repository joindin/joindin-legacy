<?php
if (isset($bid)) {
    echo form_open('blog/delete/' . $bid);
?>

    <table cellpadding="3" cellspacing="0" border="0">
    <tr>
        <td>
            Are you sure you wish to delete this blog post?<br/>
            <input type="submit" value="yes" name="confirm"> 
            <input type="submit" value="no" name="confirm">
        </td>
    </tr>
    </table>

    <?php 
    echo form_close(); 

} else {
    echo '<h1 class="title">Blog Post Removed!</h1>';
    echo '<a href="/blog">Return to blog post listing</a>';
}
