<style>
b.req_title { color: #767676; }
b.req_name { font-size: 12px; }
</style>

<h1 style="margin-top:0px;margin-bottom:2px;color:#B86F09">Joind.in API</h1>

<p>
The Joind.in API is XML based and allows for the fetching and updating of information in the service's database. Here's an example structure each request should follow:
</p>

<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
&lt;request>
        &lt;auth>
                &lt;user>$username&lt;/user>
                &lt;pass>$password&lt;/pass>
        &lt;/auth>
        &lt;action type="getdetail">
                &lt;event_id>1&lt;/event_id>
        &lt;/action>
&lt;/request>
</pre>
</div>

<p>
In our above example, you can see the <b>"auth"</b> section where you would replace $username and $password with your login information. The password should be md5 encoded. Below that there's the <b>"action"</b> section. We're making a "getdetail" call to grab the information for the given event ID.
</p>
<p>
There are three different URLs you can make requests to:
<ul>
	<li><b>api/event</b> - to get information on events
	<li><b>api/talk</b> - to get information on talks
	<li><b>api/comment</b> - to get information about individual comments
</ul>
</p>

<p>
Our sample XML above would need to be sent to "http://joind.in/api/event" to work correctly. If you send it to an incorrect URL you probably won't get quite what you're expecting. 
</p>
<p>
By default, responses will be made in an XML format. There is an optional attribute you can add to the "action" tag in your request called <b>"output"</b>. This is set to "xml" initially but it can also be set to "json" if you prefer your response in that format. Here's an example of the XML output:
</p>
<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
&lt;response>
        
&lt;/response>
</pre>
</div>
<p>
If there are any errors in the request or problems processing it, an <b>"errors"</b> array will be returned containing the message(s) about where the issue lies.
</p>

<hr/>
<p>
Below are the request types that you can make to the API including input and output variables.
</p>

<b>Request Types:</b>
<ul>
<li>Events
	<ul>
		<li><a href="">Get Event Detail</a>
		<li><a href="">Add Event</a>
	</ul>
<li>Talks
	<ul>
		<li><a href="">Get Talk Detail</a>
		<li><a href="">Get Talk Comments</a>
	</ul>
<li>Comments
	<ul>
		<li><a href="">Get Comment Detail</a>
		<li><a href="">Add Comment</a>
	</ul>
</ul>

<h2 style="color:#5181C1">Events</h2>
<b class="req_name">Get Event Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get the details for a given event number<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>event_id: integer
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>event_name: string, Name of the event
		<li>event_start: Unix timestamp
		<li>event_end: Unix timestamp
		<li>event_lat: For future use
		<li>event_long: For future use
		<li>ID: integer, ID for the event
		<li>event_loc: string, Event location
		<li>event_desc: string, Event description
		<li>active: integer, Whether the event is active or not
		<li>event_stub: string, Stub/shortcut value for event
		<li>event_tz: integer, Defines offset from GMT for event times
	</ul>
</div>

<b class="req_name">Add Event</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> addevent<br/>
<b class="req_title">Description:</b> Adds an active event<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>event_name: string, Full name of event
		<li>event_start: integer, Unix timestamp of event start time
		<li>event_end: integer, Unix timestamp of event end time
		<li>event_loc: string, Location of event
		<li>event_tz: integer, Offset of event timezone from GMT
		<li>event_desc: string, Description of event
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>msg: string, Response mesage concerning addition of event
	</ul>
</div>

<h2 style="color:#5181C1">Talks</h2>
<b class="req_name">Get Talk Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get the details for given talk number<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>talk_id: integer, ID number of the talk to fetch
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>talk_title: string, Title of the talk
		<li>speaker: string, Speaker of the talk
		<li>tid: integer, The talk ID number
		<li>eid: integer, The event ID the talk belongs to
		<li>slides_link: For future use
		<li>date_given: integer, Unix timestamp for the date talk presented
		<li>talk_desc: string, Description of the talk
		<li>lang_name: string, Language the talk given in
		<li>lang: integer, Language reference ID (internal use)
		<li>event_name: string, Name of event talk belongs to
		<li>event_tz: For future use
		<li>tavg: integer, Average rating of comments on selected talk
		<li>tcid: string, Type of entry ("Talk")
	</ul>
</div>

<b class="req_name">Get Talk Comments</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getcomments<br/>
<b class="req_title">Description:</b> Get all comments associated with a talk<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>talk_id: integer, ID number of talk to get comments for
	</ul>
<b class="req_title">Output:</b> An array of values containing the following for each comment
	<ul>
		<li>talk_id: integer, ID number of the talk comment is on
		<li>comment: string, Comments from the user
		<li>date_made: integer, Unix timestamp of when comment was posted
		<li>ID: integer, ID number of the comment
		<li>private: integer, If the comment is marked private or not
		<li>active: integer, If the comment is marked as active or not
		<li>user_id: integer, If a registered user made the comment, a non-zero value is here
		<li>uname: string, If a registered user made the comment, their username is here
	</ul>
</div>

<h2 style="color:#5181C1">Comments</h2>
<b class="req_name">Get Comment Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get detail of comment with a given ID<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>
	</ul>
</div>

<b class="req_name">Add Comment</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> addcomment<br/>
<b class="req_title">Description:</b> Add a comment to a given talk<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>talk_id: integer, ID of the talk to add the comment to
		<li>rating: integer, Rating to give the talk (range of 1-5)
		<li>comment: string, Comments to submit
		<li>private: integer, Whether to make the comment private or not
		<li>user_id: integer, The ID number of the registered user making the comment
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>
	</ul>
</div>