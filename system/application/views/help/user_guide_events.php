<h2>A User's Guide: Events</h2>

<h3>What are Events?</h3>
<p>
Events are the heart and soul of Joind.in - they're what we love and why we're around. In the strictest
sense, events are anything that happens, with or without associated sessions, that any organizer
wants to get feedback on. By listing their event on Joind.in, they can instantly get direct feedback
from those that will be or are attending the event.
</p>
<?php
$msg="<b>NOTE:</b> All events have to be approved, so please be patient - you'll receive an email when your event 
is approved.";
$this->load->view('msg_info', array('msg' => $msg)); ?>

<h3>The Event Summary</h3>
<p>
Every event on the site has something in common - the an at-a-glance page showing the details of the event.
<br/>
[pic]
<br/>
Every event detail page will have the following information:
<ul>
<li>Title of the event
<li>The event's location
<li>A description of the event
<li>A section at the bottom of the page for session links, slides and event comments
</ul>
There's a few other optional pieces of information that the event administrators can use to give even more
information about the event. These include things like:
<ul>
<li>A link back to the event's main page
<li>Related <a href="http://hastag.org">hastags</a>
<li>A "quicklink" to make it easier to link to the event
</ul>
</p>
<p>
There's one other thing on the Event Summary page that's pretty hard to miss - the <b>Attending</b> button. This
button gives you a one-click way to let the event admins - and really anyone viewing the page - an idea that 
you will be or have attended the event. You have to be logged in to use this button. See that <b>Show</b> link 
beside it? Click on that and you'll get a drop down list of those people that have clicked it to show they're
attending. This can give you a great idea of who all will be there and how many Joind.in users are attending.
</p>

<h3>Submitting Events</h3>
<p>
So you have an event you want to let the world know about (and get some feedback on)? Submitting it is easy - 
just find the "Submit your event!" button on just about any page on the site and click on it to get to the 
<a href="/event/submit">Event Submssion</a> page.
</p>
<p>
On this page you'll find fields to fill in for things like your event's name, contact information and start
and end dates. Most of the fields are free-form including the Event Location and the Event Description allowing
you the flexibility you might need. There's a few fields that are a little tricky, though, so here's more details 
on those:
</p>
<ul>
<li><b>Event Stub</b><p>Joind.in lets you customize the address people can use to find your event. For some 
event admins, using the standard addresses (like <b>http://joind.in/event/view/12</b>) is good enough for them, but we 
allow you to get a bit more creative. You can use the Event Stub field to give your event's address something
a bit more custom. The "stub" lets you create a modified address like <b>http://joind.in/event/test123</b>. In this case, 
the "<b>test123</b>" is the event stub. As long as its not in use, you can use just about anything you want as your stub.
<li><b>Private Events</b><br/>By default, all events added on Joind.in are public. Anyone can come in and comment 
on the event and the sessions inside it. If you'd rather keep things a bit more personal, you an mark your event as
private. 
</p>
<p>
By default, the event admins are included in the invite list, but other Joind.in users have to be added 
in one of two ways. Users can either visit the event's page and click on the request link to ask for an invite to the
event or the event admins can maintain the invite list themselves and add the user manually.
<li><b>Call for Papers</b><p>
Some events just happen, and others are planned. Sometimes, those planned events will have a time before they happen 
where anyone can submit ideas. This is where the Call for Papers dates can come in handy. If you check the box
and set up the dates, these dates will show up on the Event Detail page and visitors will know when the deadline is
for getting their ideas in to be heard.
</p>
<li><b>I'm an admin!</b><p>
If you're logged in when you go to the Submit Event page, you'll see something else right below the Event Stub field 
- a checkbox that asks you if you're the event administrator. By checking this, you're automatically added into the 
system and can immediately start maintaining the event.
</p>
</ul>
<p>
<b>All events have to be approved</b> and you'll recieve an email (to the admin address) once it's been approved. This
helps us keep things nice and tidy around here and make Joind.in an even more useful resource.
</p>
