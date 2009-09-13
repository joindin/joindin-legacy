<?php
/**
 * Sidebar for user and profile area
 * @package Frontend
 * @subpackage Views
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */

ob_start();
?>
    <ul>
        <li><a href="/account">Dashboard</a></li>
        <li><a href="/account/manage">Manage Account</a></li>
        <li>
			<a href="/speaker/profile">Speaker Profile</a>
			<ul>
				<li><a href="/speaker/talks">My Talks</a></li>
				<li><a href="/speaker/sessions">My Sessions</a></li>
			</ul>
		</li>
    </ul>
</div>

<h4>Administration</h4>
<div class="ctn">
    <ul>
        <?php if (user_is_admin()): ?>
	    <li><a href="/user/admin">User Admin</a></li>
	    <li><a href="/event/pending">Pending Events</a></li>
	    <li><a href="/claim">Pending Claims</a></li>
	    <?php endif; ?>
        
    </ul>


<?php 
// Register it as a sidebar
menu_sidebar('Navigation', ob_get_clean()); 
?>
 

