<?php
menu_pagetitle('Error!');
?>

<h1 class="icon-event">Error!</h1>

<p>
The page you were looking for wasn't found! Here's a few others that
might be what you're looking for - give them a try!
</p>
<p>
<b><a href="/event">Main Events List</a></b><br/>
Check out past, recent and upcoming events from our list.
</p>
<p>
<b><a href="/talk">Main Talks List</a></b><br/>
Every event has their talks but you can get a quick summary here of talks across all events
</p>
<p>
<b><a href="/about">About <?php echo $this->config->item('site_name'); ?></a></b><br/>
Learn all about <?php echo $this->config->item('site_name'); ?> and what it has to offer you as both a user and conference planner
</p>
