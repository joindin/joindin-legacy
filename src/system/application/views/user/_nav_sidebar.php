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
    </ul>
    ', count($pending_events)
    );
}
$nav=sprintf('
<ul>
<li><a href="/user/main">Dashboard</a></li>
<li><a href="/user/manage">Manage Account</a></li>
<li><a href="/user/apikey">API Keys</a></li>
<li>My Activities:<ul>
    <li><a href="/user/main">My Talks</a></li>
    <li><a href="/user/main">My Sessions</a></li>
</ul></li>
%s
</ul>', $admin_nav_lnks);

menu_sidebar('Navigation', $nav);
