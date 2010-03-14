<h2>A User's Guide: Talks</h2>

<h3>What are Talks?</h3>
<p>
Talks (or more genericly, sessions) live under <a href="/help/user_guide_events">events</a>. While events are the 
actual thing happening, talks are the sessions that happen inside of them. An easy example might be a conference
that's happening in a month. This conference is the event because it's the reason anything is happening. The sessions
hosted as a part of the conference would then be considered talks.
</p>
<p>
Talks share some characteristics with events, like the ability to add feedback, but talks let you go a step further.
When leaving feedback on a talk you select a "thumbs up" rating to go along with it. This gives a better level of
feedback for the session and lets the speaker/presenter know how well they did on a ratable scale. These ratings are
averaged up and shown in an overall ranking for the talk.
</p>
<?php
$msg="Don't see an event for the talk you're giving? Send an email to the event sponsors or staff and let them know about
Joind.in! They can add the event and your talk quickly!";
$this->load->view('msg_info', array('msg' => $msg)); ?>

<h3>The Talk Summary</h3>
<p>
When you view the detial page on an event, you might see the sessions listed under it (not all events have sessions). Clicking
on one of these links will take you to the detail page for the talk. It's easy to tell them apart - here's some of the things
that you'll find in the talk's detail:
<ul>
<li>The title and summary of the session
<li>The overall "thumbs up" rating
<li>A general description (as provided by the person giving the talk)
</ul>

There's a few special things that talks have that events don't. Most obvious is the comments listed below the details at 
the top of the page. Each of these are the details on the comments and rating that the site visitor have left. Usually the
comments will have a user's name to go along with it, but comments on talks can also be made anonymously. 
</p>
<p>
Talks can also have two other handy bits of information. To go along with every talk, there's a "Quicklink" shown up
in the details section. This is a shortened URL that speakers can use to make it easier for people to get directly to
their talk. Additionally, if the speaker has updated the session's information, there could be a link to their slides.
</p>
<?php
$msg="<b>NOTE:</b> Joind.in does not host slides of presentations. The link for the slides should point to the external
location where the presentation live (PDF, Slideshare, etc)";
$this->load->view('msg_info', array('msg' => $msg)); ?>

<h3>Types of Talks</h3>
<p>
Events can have multiple types of sessions underneath them, not just "Talks". Here's a list of some others:
<ul>
<li><b>Talk</b><br/>This is the generic type of session and the default whenever one is added. This can be any kind of
presentation.
<li><b>Social Event</b><br/>Some events will have things like a dinner in the evening or a reception before the conference
that they still want feedback on.
<li><b>Keynote</b><br/>Several larger events will have Keynote sessions given by well-known speakers that are set apart 
from the normal talks.
<li><b>Workshop</b><br/>Workshops (or tutoriails) are times where there's more hands-on activity than just a speaker 
delivering a talk
<li><b>Event Related</b><br/>This type is a sort of catch-all. If there's something about the event the organizers want 
feedback on (like the internet access or the quality of the food) they can create a session with this type for direct 
feedback.
</ul>
</p>

<h3>Claiming Talks</h3>
<p>
If an event has been posted and the sessions added underneath, the easiest way for you to lay claim to your session(s) is 
to use the <b>Claim this talk</b> button on the talk's detail page (right below the talk's details). This sends a 
notification to the event admins that there's a pending claim. They'll <b>have to approve it</b> before it can be 
officially yours, but once it's approved, you'll see it in your list.
</p>
<p>
Once your claim has been accepted, you can then visit the talk's detail page and make any updates you might need. This inclues
adding in the link to your presentation if you'd like it to be visible.
</p>

<h3>Adding Talks</h3>
<p>
To be able to add talks to an event, you must be an event admin. Joind.in does not currently have the ability for users to submit
their sessions to a conference. If you are a part of an event and do not see your session listed. Use the <b>Contact Event Admins</b>
button on the right side of the event's detail page to send them a message.
</p>


