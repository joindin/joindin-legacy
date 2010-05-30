<h2>Event Admin Cheat Sheet</h2>

<a name="top"></a>
<ul>
<li><a href="#add_session">Adding a session</a>
<li><a href="#approve_claims">Approve session claim requests</a>
<li><a href="#evt_import">Importing session information</a>
<li><a href="#evt_voting">Pre-event Voting</a>
<li><a href="#session_types">Session types</a>
</ul>
<hr/>

<a name="add_session"></a>
<h3>Adding a session</h3>
<p>
To add a new session to an event, log in and navigate to the event's detail page. Look for the "Add new talk"
button and click it to get the new session form.
<br/>
<a href="#top">Back to top</a>
</p>
<br/>

<a name="approve_claims"></a>
<h3>Approve session claim requests</h3>
<p>
When you get an email saying that you have a pending claim request, you can click on the link in the email to 
go straigt to the "claims" page. Once you're there, you can evaluate if the claim needs to be approved or not (
based on the speaker(s) of the talk and the person requesting).
<br/>
<a href="#top">Back to top</a>
</p>
<br/>

<a name="evt_import"></a>
<h3>Importing session information</h3>
<p>
With the XML import feature (<a href="<?php echo $this->config->site_url(); ?>about/import">more info here</a>) you can define the XML
structure with your session data. To import your data, navigate to the event's detail page and look for the 
"Import Event Info" button. Click on that and select your XML file to submit.
<br/>
<a href="#top">Back to top</a>
</p>
<br/>

<a name="evt_voting"></a>
<h3>Pre-event Voting</h3>
<p>
If you have an event that wants to use voting as a part of selecting the sessions for the event, you can enable
the pre-event voting in the event's settings. To enable it, go to your event's detail page and click to edit the 
event. When you do, you'll see a checkbox to enable the feature. With it on, users will be able to add thier votes 
to the session before the event starts.
<br/>
<a href="#top">Back to top</a>
</p>
<br/>

<a name="evt_voting"></a>
<h3>Session Types</h3>
<p>
There's several different session types you can use for your event, some with some special features:
<ul>
<li><b>Talk</b> - This is a normal, generic session type and will be the type used most. It lets you set up 
the session information like speaker, title, etc. These will be listed in the main Talks list on the event's
detail page.
<li><b>Social Event</b> - Social events can be things like a pre-conference speaker dinner or an evening activity 
that the event organizers might want some feedback on. This allows them to split this out from the rest of the talk 
list.
<li><b>Keynote</b> - Keynote sessions can be marked as something different so it's easier to find them among the list. 
They work the same way as normal sessions.'
<li><b>Event Related</b> - This session type is another one of the special ones. When you set up an event related 
session, it will show up in the "Event Related" tab on the event's detail page. This type of session could be used
for things that aren't really sessions/events - like the wifi provided or the bagels they had out for breakfast - but 
the organizers still want some input on.
<li><b>Workshop/Tutorial</b> - These are generally longer sessions where more in-depth looks at specific technologies.
</ul>
<a href="#top">Back to top</a>
</p>