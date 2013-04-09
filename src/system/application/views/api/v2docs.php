<?php 
menu_pagetitle('API v2 Documentation');
$base_url = $this->config->config['base_url']; 
?>

<h1>API Docs: V2 API</h1>

<h2>Overview</h2>

<p>Joind.in is offering a HTTP web service to give clean, robust access to the data contained in the application to consuming devices.  It follows a RESTful style, is available in HTML and JSON formats, and uses OAuth v2 for authentication where this is needed (all data publicly visible on the site is available via the API without authentication). We also have an HTML output handler and hypermedia links, so you can click around the API in your browser: <a href="http://api.joind.in">http://api.joind.in</a>.</p>

<h2>Interactive Documentation</h2>

<p>We use <a href="https://github.com/mashery/iodocs">IODocs from Mashery</a> to give an interactive way of viewing the API and testing it out.  To use this, you'll need a valid access token which you enter at the top of the page.</p>

<p><a href="http://test.joind.in/user/oauth_allow?api_key=05348fd4f886a0785d69f1e8ec4ff4&callback=http://joind.in/api/v2docs">Click here to generate your access token</a> <?php if(isset($_GET['access_token']) && $_GET['access_token']) echo " access token: " . $_GET['access_token']; ?></p>

<p>Copy the token, and <a href="http://lornajane-iodocs.heroku.com/joindinv2">try the interactive docs</a>/  Or, keep reading for more detailed information.</p>


<h2>Global Parameters</h2>

<p>There are a few parameters supported on every request:
<ul><li><b>verbose: </b> set to <b>yes</b> to see a more detailed set of data in the responses.  This works for individual records and collections.</li>
<li><b>start: </b> for responses which return lists, this will offset the start of the result set which is returned.  The default is zero; use in conjuction with <b>resultsperpage</b></li>
<li><b>resultsperpage: </b>for responses which return lists, set how many results will be returned.  The default is currently 20 records; use with <b>start</b> to get large result sets in manageable chunks</b></li>
<li><b>format: </b>set this to <b>html</b> or <b>json</b> to specify what format the response should be in (preferably use the Accept Header, alternatively you can pass this param)</li>
</ul></p>


<h2>Data Formats</h2>

<p>The service currenty supports <b>JSON</b> and <b>HTML</b> only, although these can very easily be expanded upon in future.  The service will guess from your accept header which format you wanted.  In the event that this is not behaving as expected, simply add the <b>format</b> parameter to specify which format should be returned.</p>

<p>If you want to use the data provided by this API from JavaScript, we offer support for <b>JSONP</b>.  To use this, request json format data and pass an additional <b>callback</b> parameter; the results will be the usual JSON but surrounded with the function you named.</p>

<p>Where there are links to other resources, and for pagination, you will find those links as part of the response.  The pagination links look something like this:
<blockquote>
<ul>
<li><strong>meta:</strong> <ul>

<li><strong>count:</strong> 20</li>

<li><strong>this_page:</strong> <a href="https://api.joind.in/v2/events/603/talks?resultsperpage=20&amp;start=0">https://api.joind.in/v2/events/603/talks?resultsperpage=20&amp;start=0</a></li>

<li><strong>next_page:</strong> <a href="https://api.joind.in/v2/events/603/talks?resultsperpage=20&amp;start=20">https://api.joind.in/v2/events/603/talks?resultsperpage=20&amp;start=20</a></li>

</ul>

</li>

</ul>

</blockquote>

<h2>Authentication</h2>

<p>You only need to authenticate if you're adding or editing data (including comments) or want to access private data.  For most operations, particularly just retrieving information, authentication is not required.</p>

<p>This API uses OAuth2.  To authenticate you will need the following:
<ol><li>Every app must first register for an API key and give the callback that they will use to send users to.  To register an API key, sign in to joind.in and visit: <a href="<?php echo $base_url; ?>user/apikey"><?php echo $base_url; ?>user/apikey</a>.  These are associated with your user account, you can have as many as you like and you can delete them at any time.</li>
<li>When you want a user to grant you access to their data, send them to: <a href="<?php echo $base_url; ?>user/oauth_allow"><?php echo $base_url; ?>user/oauth_allow</a> with the following query variables on the URL:
    <ul><li><code>api_key</code> The key you registered for in step 1 (the secret isn't currently used)</li>
    <li><code>callback</code> The callback URL to send the user to afterwards.  This can be a device URL and it <b>must match the URL you registered</b> in step 1 (exactly match)</li>
    <li><code>state</code> (optional) Whatever you pass in here will be passed back with the user when we redirect them back to you.  Use it however you like</li></ul></li>
    <li>When the user is sent to the redirect URL, it will contain one additional parameter: <code>access_token</code>.  Capture this and store it - this is a per-user token.</li>
    <li>To make requests with access to that user's data, add the access token into an authorisation header.  The format should be: <br />
<code>Authorization: OAuth [access_code]</code></li></ul><br />

<p>If you have any questions or problems, just <a href="http://joind.in/about/contact">let us know</a>, this is new functionality and feedback is more than welcome.</p>

<h2>Service Detail</h2>

<p>Examples shown in HTML format.  The JSON response holds identical data, passed through json_encode rather than pretty-printed</p>

<h3>Request: GET /</h3>

<p><a href="https://api.joind.in">try it</a></p>

This is your starting point and will show you where you can go:
<blockquote>
<ul>
<li><strong>events:</strong> <a href="http://api.joind.in/v2.1/events">http://api.joind.in/v2.1/events</a></li>
<li><strong>hot-events:</strong> <a href="http://api.joind.in/v2.1/events?filter=hot">http://api.joind.in/v2.1/events?filter=hot</a></li>
<li><strong>upcoming-events:</strong> <a href="http://api.joind.in/v2.1/events?filter=upcoming">http://api.joind.in/v2.1/events?filter=upcoming</a></li>
<li><strong>past-events:</strong> <a href="http://api.joind.in/v2.1/events?filter=past">http://api.joind.in/v2.1/events?filter=past</a></li>
<li><strong>open-cfps:</strong> <a href="http://api.joind.in/v2.1/events?filter=cfp">http://api.joind.in/v2.1/events?filter=cfp</a></li>
</ul>
</blockquote>


<h3>Request: GET /events</h3>
<h3>Request: GET /events/[id]</h3>

<p><a href="https://api.joind.in/v2.1/events">try it</a></p>

<p>Shows a list of events, with a variety of filter/sorting behaviour supported (see above entry).  The default is all events sorted by date descending.  As ever, you can use the links to get to other information, and the <b>verbose</b>, <b>start</b>, <b>requestsperpage</b> and <b>format</b> parameters as you need to.  The "attending" field will be set to 1 when there is an authenticated user who is marked as attending this event.</p>

Each result looks something like this:</p>
<blockquote>
<ul>
<li><strong>name:</strong> Whisky Web Conference</li>
<li><strong>start_date:</strong> 2012-04-12T00:00:00+01:00</li>
<li><strong>end_date:</strong> 2012-04-14T23:59:59+01:00</li>
<li><strong>description:</strong> THE WEB CONFERENCE IN SCOTLAND

The inaugural Whisky Web conference kicks off in Edinburgh on the 13th and 14th of April 2012. A web conference created for the web community, by the web community; Whisky Web will have something to offer everyone who works with the web, be they a designer, a developer or something in between. This is an amazing opportunity to get your geek on in Scotland's inspiring capital.

More at http://whiskyweb.co.uk/</li>
<li><strong>href:</strong> </li>
<li><strong>attendee_count:</strong> 35</li>
<li><strong>attending:</strong> 1</li>
<li><strong>event_comments_count:</strong> 4</li>
<li><strong>icon:</strong> logo.90x90_.png</li>
<li><strong>tags:</strong> <ul>
<li><strong>0:</strong> php</li>
<li><strong>1:</strong> ruby</li>
<li><strong>2:</strong> python</li>
<li><strong>3:</strong> web</li>
</ul>
</li>
<li><strong>uri:</strong> <a href="http://api.joind.in/v2.1/events/886">http://api.joind.in/v2.1/events/886</a></li>
<li><strong>verbose_uri:</strong> <a href="http://api.joind.in/v2.1/events/886?verbose=yes">http://api.joind.in/v2.1/events/886?verbose=yes</a></li>
<li><strong>comments_uri:</strong> <a href="http://api.joind.in/v2.1/events/886/comments">http://api.joind.in/v2.1/events/886/comments</a></li>
<li><strong>talks_uri:</strong> <a href="http://api.joind.in/v2.1/events/886/talks">http://api.joind.in/v2.1/events/886/talks</a></li>
<li><strong>website_uri:</strong> <a href="http://joind.in/event/view/886">http://joind.in/event/view/886</a></li>
<li><strong>humane_website_uri:</strong> <a href="http://joind.in/event/whiskyweb">http://joind.in/event/whiskyweb</a></li>
</ul>
</blockquote>

<h3>Request: GET /events/[id]/talks</h3>
<h3>Request: GET /talks/[id]</h3>

<p><a href="https://api.joind.in/v2.1/events/886/talks">try it</a></p>

<p>Following the link to the talks for an event gives a list.  The <b>format</b>, <b>verbose</b>, <b>start</b> and <b>requestsperpage</b> parameters are valid.  Each talk entry will look something like this:</p>

<blockquote>
<ul>
<li><strong>talk_title:</strong> Estimation, or &quot;How To Dig Your Own Grave&quot;</li>
<li><strong>talk_description:</strong> Clients need to know how much a project will cost. Waterfall development is always late and over-budget. Agile development is done when it's done. You're left with estimates that you know are too low and then you squeeze them anyway. It shouldn't be this way. We'll look at how this happens, early warning signs, ways out and ways of avoiding it in the first place.</li>
<li><strong>slides_link:</strong> <a href="http://merewood.org/estimation-or-how-to-dig-your-own-grave/">http://merewood.org/estimation-or-how-to-dig-your-own-grave/</a></li>
<li><strong>language:</strong> English - UK</li>
<li><strong>start_date:</strong> 2012-04-13T09:50:00+01:00</li>
<li><strong>average_rating:</strong> 5</li>
<li><strong>comments_enabled:</strong> 1</li>
<li><strong>comment_count:</strong> 12</li>
<li><strong>speakers:</strong> <ul>
<li><strong>0:</strong> <ul>
<li><strong>speaker_name:</strong> Rowan Merewood</li>
<li><strong>speaker_uri:</strong> <a href="http://api.joind.in/v2.1/users/118">http://api.joind.in/v2.1/users/118</a></li>
</ul>
</blockquote>

<h3>Request: GET /talks/[talk_id]/comments</h3>
<h3>Request: GET /talks/[talk_id]/comments/[comment_id]</h3>

<p>The talk comments (note that event comments are a different thing) include a rating and comment, and if the comment was made by a logged-in user, a link to their user account. The <b>format</b>, <b>start</b> and <b>requestsperpage</b> parameters are valid, and the record for each comment looks something like this:</p>

<p><a href="http://api.joind.in/v2.1/talks/6287/comments">try it</a></p>

<blockquote>
<ul>
<li><strong>rating:</strong> 5</li>
<li><strong>comment:</strong> He gave some interesting pieces of info and theories that are applicable to professional world!

Very Good</li>
<li><strong>user_display_name:</strong> Martin Moscosa</li>
<li><strong>talk_title:</strong> Estimation, or &quot;How To Dig Your Own Grave&quot;</li>
<li><strong>created_date:</strong> 2012-04-14T13:45:22+01:00</li>
<li><strong>uri:</strong> <a href="http://api.joind.in/v2.1/talk_comments/19450">http://api.joind.in/v2.1/talk_comments/19450</a></li>
<li><strong>verbose_uri:</strong> <a href="http://api.joind.in/v2.1/talk_comments/19450?verbose=yes">http://api.joind.in/v2.1/talk_comments/19450?verbose=yes</a></li>
<li><strong>talk_uri:</strong> <a href="http://api.joind.in/v2.1/talks/6287">http://api.joind.in/v2.1/talks/6287</a></li>
<li><strong>talk_comments_uri:</strong> <a href="http://api.joind.in/v2.1/talks/6287/comments">http://api.joind.in/v2.1/talks/6287/comments</a></li>
<li><strong>user_uri:</strong> <a href="http://api.joind.in/v2.1/users/18140">http://api.joind.in/v2.1/users/18140</a></li>
</ul>
</blockquote> 

<h3>Request: GET /events/[event_id]/comments</h3>
<h3>Request: GET /events/[event_id]/comments/[comment_id]</h3>

<p>The comments show who made the comment and their comment and rating. The <b>format</b>, <b>start</b> and <b>requestsperpage</b> parameters are valid, and the record for each comment looks something like this:</p>

<p><a href="http://api.joind.in/v2.1/events/886/comments">try it</a></p>

<blockquote>
<ul>
<li><strong>comment:</strong> This was a great opportunity to learn. The selection of speakers was very impressive, and the talks I attended were all interesting.

All credit to the organisers for making it happen, especially at such short notice.
For me, as a local (and a Scot), the low price made it a &quot;no-brainer&quot; to attend at my own cost
(for that price I didn't expect lunch and dinner to be included too).

Well done and thanks to the various sponsors (including Joe) - you deserved the unashamed plugs at the end of the day !

I hope it will happen again next year.</li>
<li><strong>created_date:</strong> 2012-04-15T12:51:25+01:00</li>
<li><strong>user_display_name:</strong> Rory Davies</li>
<li><strong>user_uri:</strong> <a href="http://api.joind.in/v2.1/users/18152">http://api.joind.in/v2.1/users/18152</a></li>
<li><strong>comment_uri:</strong> <a href="http://api.joind.in/v2.1/event_comments/679">http://api.joind.in/v2.1/event_comments/679</a></li>
<li><strong>verbose_comment_uri:</strong> <a href="http://api.joind.in/v2.1/event_comments/679?verbose=yes">http://api.joind.in/v2.1/event_comments/679?verbose=yes</a></li>
<li><strong>event_uri:</strong> <a href="http://api.joind.in/v2.1/events/886">http://api.joind.in/v2.1/events/886</a></li>
<li><strong>event_comments_uri:</strong> <a href="http://api.joind.in/v2.1/events/886/comments">http://api.joind.in/v2.1/events/886/comments</a></li>
</ul>
</blockquote>

<h3>Request: GET /users/[user_id]</h3>

<p>The user resource is available where the user is the speaker for a talk, a host of an event, and where they have left comments logged in as themselves.  It includes links to the talks given by this user and the events they attended</p>

<p><a href="http://api.joind.in/v2.1/users/118">try it</a></p>

<blockquote>
<ul>
<li><strong>username:</strong> rowan_m</li>
<li><strong>full_name:</strong> Rowan Merewood</li>
<li><strong>twitter_username:</strong> rowan_m</li>
<li><strong>uri:</strong> <a href="http://api.joind.in/v2.1/users/118">http://api.joind.in/v2.1/users/118</a></li>
<li><strong>verbose_uri:</strong> <a href="http://api.joind.in/v2.1/users/118?verbose=yes">http://api.joind.in/v2.1/users/118?verbose=yes</a></li>
<li><strong>website_uri:</strong> <a href="http://joind.in/user/view/118">http://joind.in/user/view/118</a></li>
<li><strong>talks_uri:</strong> <a href="http://api.joind.in/v2.1/users/118/talks/">http://api.joind.in/v2.1/users/118/talks/</a></li>
<li><strong>attended_events_uri:</strong> <a href="http://api.joind.in/v2.1/users/118/attended/">http://api.joind.in/v2.1/users/118/attended/</a></li>
</ul>
</blockquote>

<h3>Request: GET /users/[user_id]/attended</h3>

<p>A list of all the events a user has been to (where they have marked themselves as attending the event).  The output format is exactly as the other events</p>

<p><a href="http://api.joind.in/v2.1/users/118/attended/">try it</a></p>

<h3>Request: GET /users/[user_id]/talks</h3>

<p>All the talks given by this user, in the same format as the other talk results</p>

<p><a href="http://api.joind.in/v2.1/users/118/talks/">try it</a></p>


