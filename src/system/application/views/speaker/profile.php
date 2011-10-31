<?php
/*
 * Add or edit the speaker's profile
 */
menu_pagetitle('Manage Speaker Profile');

$this->load->view('user/_nav_sidebar');
?>

<div class="menu">
    <ul>
    <li class="active"><a href="/speaker/profile">Speaker Profile</a>
    <li><a href="/speaker/access">Profile Access</a>
    </ul>
    <div class="clear"></div>
</div>


<div class="box">
    <?php if (empty($pdata)): ?>
    <p style="text-align: center;">
        You do not have a speaker profile yet. Go create one!<br />
    </p>
    <?php endif; ?>
    <p style="text-align: center;">
    <?php if (empty($pdata)): ?>
        <a class="btn-big btn-success" href="/speaker/edit">Create speaker profile</a>
    <?php else: ?>
        <a class="btn-big btn" href="/speaker/edit">Edit speaker profile</a>
    <?php endif; ?>
    </p><br/>

    <h2>Current speaker profile</h2>
    <p>
        <?php
        if (empty($pdata)) {
            echo 'No speaker profile found! Set one up!';
        } else {
            $d=$pdata[0];
            $titles=array(
                'full_name'=>'Full Name','contact_email'=>'Email','website'=>'Website',
                'blog'=>'Blog','phone'=>'Phone','city'=>'City','zip'=>'Zip Code',
                'street'=>'Street','job_title'=>'Job Title','bio'=>'Bio'
            );
            ?>
            <table cellpadding="0" cellspacing="0" border="0">
            <tr><td>
            <?php
            if (isset($d->profile_pic)) { echo '<img src="'.$d->profile_pic.'"/>'; 
                } else { 
                    echo '<div style="width:100px;height:100px;border:1px solid #919191;text-align:center;background-color:#CCCCCC">
                <p style="line-height:90px;color:#919191;font-weight:bold">No Image</p></div>'; 
            }
            ?>
            </td><td valign="top" style="padding-left:10px;vertical-align:top">
            <table cellpadding="0" cellspacing="0" border="0">
            <?php foreach ($titles as $k=>$v) { ?>
            <tr>
                <td style="padding:3px"><?php echo $v;?>:</td><td><?php echo $d->$k; ?></td>
            </tr>
            <?php } ?>
            </table>
            </td></tr></table>
            <?php
        }
        ?>
    </p>
</div>

