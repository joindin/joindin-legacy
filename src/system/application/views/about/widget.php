<?php
menu_pagetitle('About : Widgets');
?>

<script src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/inc/js/widget.js"></script>
<h2>Widgets</h2>
<p>
Widgets offer a way for you to embed a little bit of the site back into yours and 
share events and talks with your site's visitors. They come in the form of a few different
sized blocks that can display the information about any event or session on the site. This
info includes the name of the event/session, the location of the event and, if it's a session,
the current rating.
</p>
<p>
Here's an example:
<script>joindin.display_talk_large(175,"ji-talk-large");</script>
<div id="ji-talk-large"></div>
</p>
<br/><br/>
<p>
This is a sample talk widget giving the title, speaker, event and the current rating for the
session. Using the widgets is super-simple too...there's just two steps:
</p>
<ul>
<li>Include the main widget javascript file: http://<?php echo $_SERVER['SERVER_NAME']; ?>'/inc/js/widget.js
<li>Call one of the functions below to load the widget of your choice.
</ul>
<p>
Here's an example of the HTML to drop a widget into your page:
</p>
<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
&lt;script src="http://<?php echo $_SERVER['SERVER_NAME']?>/inc/js/widget.js" id="joindin_widget">
&lt;/script>
&lt;script>joindin.display_talk_large(175);&lt;/script>
</pre>
</div>
<br/>
<p>
By default, the widget will append to the location of the script tag with an ID of "joindin_widget". You can change this,
though, with the optional second parameter:
</p>
<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
&lt;div id="mydiv">&lt;/div>
&lt;script>joindin.display_talk_large(175,'mydiv')&lt;/script>
</pre>
</div>
<br/>
<p>
By putting "mydiv" into the second parameter, it tells the widget to push the output into the div named "mydiv". You 
can use this to put the widget anywhere on the page you want. Plus, you can call multiple "display" functions to
load multiple widgets all in one page.
</p>
<br/>
<h2>Types of Widgets</h2>
<ul>
<li><a href="#talk_small">Talk - Small</a>
<li><a href="#talk_large">Talk - Large</a>
<li><a href="#event_large">Event - Large</a>
</ul>
<a name="talk_small"></a>
<p>
<h3>Talk Small</h3>
The small version of the talk/session widget includes: talk title, event name given at, current rating.
Called with talk/session ID number.
<br/><br/>
joindin.display_talk_small(num)
</p>

<a name="talk_large"></a>
<p>
<h3>Talk Large</h3>
The larger version of the talk/session widget includes: talk title, speaker name(s), event name given at, current rating.
Called with talk/session ID number.
<br/><br/>
joindin.display_talk_large(num)
</p>

<a name="event_large"></a>
<p>
<h3>Event Large</h3>
The larger version of the event widget includes: event title, location, start and end dates and the event icon if
it has one.
Called with event ID number.
<br/><br/>
joindin.display_event_large(num)
</p>
