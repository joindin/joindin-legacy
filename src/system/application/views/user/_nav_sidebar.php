<?php
/* 
 * User pages navigation
 */
$admin_nav_lnks='';
if (user_is_admin()) {
    $admin_nav_lnks=sprintf('
    <li><b>Admin Links</b>
    <ul>
    <li><a href="/user/admin">User Admin</a>
    <li><a href="/event/pending">Pending Events</a> (%s)
    <li><a href="/talk/claim">Talk Claims</a>
    <li><a href="/event/claims">Event Claims</a> (%s)
    </ul>
    ', count($pending_events), count($event_claims)
    );
}
$nav=sprintf('
<ul>
<li><a href="/user/main">Dashboard</a>
<li><a href="/user/manage">Manage Account</a>
<li><a href="/speaker/profile">Speaker Profile</a>
<ul>
    <li><a href="/user/main">My Talks</a>
    <li><a href="/user/main">My Sessions</a>
</ul>
%s
</ul>', $admin_nav_lnks);

menu_sidebar('Navigation', $nav);
?>
