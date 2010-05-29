<h2>Event Administration</h2>

<h3>So you're an admin, now what?</h3>
<p>
If you're reading this, there's a pretty good chance that you've either just submitted (and gotten approved) a
new event on the site or someone else has just added you to their admin list. Either way, you have a lot of new
features to explore about the event you're in charge of - there's a lot to cover so let's get started.
</p>
<p>
There's a few key spots for event admins to look at when they're looking for these new features. One is the 
right-hand sidebar, a box labeled <b>Event Admin</b> will contain some handy links to work with the event 
and a place to add and remove other event admins.
</p>

<h3>Editing an Event</h3>
<p>
If you were the one that submitted the event, the Edit Event page will look very familiar. To edit your event, 
look to that right-hand sidebar box and click on the <b>Edit Event</b> link. The page you'll be presented with 
includes most of the same options you'll find on the <a href="/event/submit">Event Submit</a> page, with a few exceptions:
</p>
<ul>
<li><b>Event Icon</b><p>
On every <b>Event Detail</b> page you'll notice a spot for an icon that can personalize your event even more. While
you can't submit this with the original event request, you can use the <b>Edit Event</b> form to upload your image.
Images can be in any image type but the need to be <b>90 pixels by 90 pixels (square)</b>. If the image is the wrong
size, the form will kick it back and let you know.
</p>
<li><b>Event Link</b><p>
This one's pretty easy, but bears mentioning - the <b>Event Link</b> field gives you a chance to point users back to the 
official website for the event. This is a quick link for them to either head back to where they came from (the event 
did link back to Joind.in, right?) or to point them in the right direction to get the full details. You can only define 
<b>one link</b> in this field.
</p>
<li><b>Event Hastag(s)</b><p>
If you're familiar with the idea of <a href="http://hashtags.org">hashtags</a>, you can use this form right away. Just
drop your tags in there, comma seperated. Those will automatically be linked to the hashtags website as tags for
easy searching. 
</p>
<p>
For those not familar with hastags, here's a brief rundown - the idea of tagging content isn't anything 
new. Along these lines, the idea for hashtags was born, a more content-agnostic approach that uses 
<a href="http://twitter.com">Twitter</a> as a tracking method. People can search for the tags and find related messages.
By providing your attendees (and other visitors) these tags, they can better track everything about your event.
</p>
</ul>

<h3>Importing Event Information</h3>
<p>
Adding and editing the event is a pretty painless process, but if you have more than one session in your event, the 
prospect of adding in all of those sessions by hand can be intimidating. We've got something that can make it a lot 
easier to drop your content into the site. See that little button that says "Import Event Info"? If you click on that
you'll see a page that lets you upload a file with the details of your sessions.
</p>
<p>
The tricky part about the process is the format for the file - it's a custom XML format that you'll need to match against
to get our site to import it all and drop it into the event. You can read more about this custom format over on the 
<a href="/about/import">Import page</a> in the <b>About</b> section. 
</p>
<?php
$msg="Or, for the more technically minded, you can grab the <a href=\"/inc/xml/schema_event.xml\">schema we're validating against</a>";
$this->load->view('msg_info', array('msg' => $msg)); ?>
</p>

