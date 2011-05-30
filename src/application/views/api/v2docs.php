<?php menu_pagetitle('API v2 Documentation'); ?>

<h1>API Docs: V2 API</h1>

<h2>Overview</h2>

<p>Joind.in is offering an HTTP web service to give clean, robust access to the data contained in the application to consuming devices.  It follows a RESTful style, is available in HTML and JSON formats, and uses OAuth v1.0a for authentication where this is needed (all data publicly visible on the site is available via the API without authentication).  Hyperlinks are provided the responses to allow you to easily locate related data.

This document gives information about the functionality of the API and how to use it.</p>


<h2>Global Parameters</h2>

<p>There are a few parameters supported on every request:
<ul><li><b>verbose: </b> set to <b>yes</b> to see a more detailed set of data in the responses</li>
<li><b>start: </b> for responses which return lists, this will offset the start of the result set which is returned.  Use in conjuction with <b>resultsperpage</b></li>
<li><b>resultsperpage: </b>for responses which return lists, set how many results will be returned.  Use with <b>start</b> to get large result sets in manageable chunks</b></li>
<li><b>format: </b>set this to <b>html</b> or <b>json</b> to specify what format the response should be in.</li>
</ul></p>


<h2>Data Formats</h2>

<p>The service currenty supports <b>JSON</b> and <b>HTML</b> only, although these can very easily be expanded upon in future.  The service will guess from your accept header which format you wanted.  In the event that this is not working correctly then simply add the <b>format</b> parameter to specify which format should be returned.</p>


<h2>Service Detail</h2>

<p>Examples shown in HTML format.  The JSON response holds identical data, passed through json_encode rather than pretty-printed</p>

<h3>Request: GET /</h3>

<p><a href="http://api.joind.in">try it</a></p>

This is your starting point and will show you where you can go:
<blockquote>
<strong>events: </strong><a href="http://api.joind.in/v2/events">http://api.joind.in/v2/events</a><br />

<strong>count: </strong>1<br />
</blockquote>



<h3>Request: GET /v2/events</h3>
<h3>Request: GET /v2/events/[id]</h3>

<p><a href="http://api.joind.in/v2/events">try it</a></p>

<p>Shows a list of events, sorted by start time descending.  We will offer other views of events, with different filters and sorting, in time.

Each result looks something like this:</p>
<blockquote>
<strong>event_id: </strong>110<br />

<strong>name: </strong>PHPBenelux Conference 2010<br />

<strong>start_date: </strong>2010-01-29T00:00:00+00:00<br />

<strong>end_date: </strong>2010-01-30T23:59:59+00:00<br />

<strong>description: </strong>PHPBenelux, in co-operation with its gold sponsors Microsoft and Ibuildings, is proud to announce the PHPBenelux 2010 PHP Conference. This conference, aimed at professional PHP users throughout Western-Europe, will be held on Saturday January 30th 2010 at the Best Western Hotel Â‘Ter ElstÂ’ in Edingen (Antwerp, Belgium). This English spoken conference will last for one day, and will bring you the latest topics on how to empower PHP in your business, and how to improve your capabilities as a PHP Application Developer.<br />

<strong>href: </strong><a href="http://conference.phpbenelux.eu/">http://conference.phpbenelux.eu/</a><br />

<strong>icon: </strong>phpbenelux2010.gif<br />

<strong>uri: </strong><a href="http://api.joind.in/v2/events/110">http://api.joind.in/v2/events/110</a><br />

<strong>verbose_uri: </strong><a href="http://api.joind.in/v2/events/110?verbose=yes">http://api.joind.in/v2/events/110?verbose=yes</a><br />

<strong>comments_link: </strong><a href="http://api.joind.in/v2/events/110/comments">http://api.joind.in/v2/events/110/comments</a><br />

<strong>talks_link: </strong><a href="http://api.joind.in/v2/events/110/talks">http://api.joind.in/v2/events/110/talks</a><br />

<br />

<br />


<strong>count: </strong>1<br />
</blockquote>



<h3>Request: GET /events/[id]/talks</h3>
<h3>Request: GET /talks/[id]</h3>

<p><a href="http://api.joind.in/v2/events/110/talks">try it</a></p>

<p>Following the link to the talks for an event gives a list.  The <b>format</b>, <b>start</b> and <b>requestsperpage</b> parameters are valid.  Each talk entry will look something like this:</p>

<blockquote>
<strong>0: </strong><br />

<strong>talk_id: </strong>1249<br />

<strong>event_id: </strong>110<br />

<strong>talk_title: </strong>Passing the Joel Test in the PHP World<br />

<strong>talk_description: </strong>The Joel Test is a series of 12 steps which, according to software guru Joel Spolsky, every team should follow in order to create succcessful code. The steps include things like using source control, having a bug database and using the best tools. This session takes a look at how relevant his steps are to PHP development today, and the tools available to help us achieve his recommendations. We’ll look at the packages available for the steps where software can help and discuss ways to implement process and political changes to facilitate some others - finally we’ll talk about which don’t apply and invent some steps to replace them.<br />

<strong>start_date: </strong>2010-01-30T00:00:00+00:00<br />

<strong>speaker_name: </strong>Lorna Mitchell<br />

<strong>uri: </strong><a href="http://api.joind.in/v2/talks/1249">http://api.joind.in/v2/talks/1249</a><br />

<strong>verbose_uri: </strong><a href="http://api.joind.in/v2/talks/1249?verbose=yes">http://api.joind.in/v2/talks/1249?verbose=yes</a><br />

<strong>comments_link: </strong><a href="http://api.joind.in/v2/talks/1249/comments">http://api.joind.in/v2/talks/1249/comments</a><br />

<strong>event_link: </strong><a href="http://api.joind.in/v2/events/110">http://api.joind.in/v2/events/110</a><br />

<br />

<strong>count: </strong>1<br />
</blockquote>



