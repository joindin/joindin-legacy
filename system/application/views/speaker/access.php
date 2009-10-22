<?php
/* 
 * Define access to the elements of a speaker's profile
 * 
 */

menu_pagetitle('Manage Speaker Profile Access');

$this->load->view('user/_nav_sidebar');

?>
<div class="menu">
	<ul>
	<li><a href="/speaker/profile">Speaker Profile</a>
	<li class="active"><a href="/speaker/access">Profile Access</a>
	</ul>
	<div class="clear"></div>
</div>

<div class="box">
	<p style="text-align: center;">
	    <a class="btn-big btn" href="/speaker/access/add">Add Profile Access</a>
	</p>
</div>

<h2>Current Access</h2>
<?php
//print_r($access_data);
?>