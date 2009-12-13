<style>
b.req_title { color: #767676; }
b.req_name { font-size: 12px; }
</style>

<h1 style="margin-top:0px;margin-bottom:2px;color:#B86F09">Joind.in API</h1>

<p>
The Joind.in API is XML based and allows for the fetching and updating of information in the service's database. Here's an example structure each request should follow:
</p>

<h3>Sample Request</h3>
<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
<?php echo escape('<request>
        <auth>
                <user>$username</user>
                <pass>$password</pass>
        </auth>
        <action type="getdetail">
                <event_id>1</event_id>
        </action>
</request>'); ?>
</pre>
</div>

<p>
In our above example, you can see the <b>"auth"</b> section where you would replace $username and $password with your login information. The password should be md5 encoded. Below that there's the <b>"action"</b> section. We're making a "getdetail" call to grab the information for the given event ID.
</p>
<b>
<b>Note:</b></b> all requests to the Joind.in API require a valid login to be passed in via the "auth" credentials. The only anonymous method is the API status request (api/status).
</p>
<br/><br/>
<h3>Types</h3>
<p>
There are four different URLs you can make requests to:
<ul>
	<li><b>api/status</b> - to get the current status of the web service
	<li><b>api/event</b> - to get information on events
	<li><b>api/talk</b> - to get information on talks
	<li><b>api/comment</b> - to get information about individual comments
</ul>
</p>

<p>
Our sample XML above would need to be sent to "http://joind.in/api/event" to work correctly. If you send it to an incorrect URL you probably won't get quite what you're expecting. 
</p>

<h3>Responses</h3>
<p>
By default, responses will be made in an XML format. There is an optional attribute you can add to the "action" tag in your request called <b>"output"</b>. This is set to "xml" initially but it can also be set to "json" if you prefer your response in that format. Here's an example of the XML output:
</p>
<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
<?php echo escape('<response>
	<item>
		<talk_title>My Test Talk</talk_title>
		<talk_desc>This is a sample talk description</talk_desc>
		<ID>42&lt;/ID>
	</item>
</response>'); ?>
</pre>
</div>
<p>
If there are any errors in the request or problems processing it, an <b>"errors"</b> array will be returned containing the message(s) about where the issue lies.
</p>

<hr/>
<h3>Request Types</h3>
<p>
Below are the request types that you can make to the API including input and output variables.
</p>
<a name="top"></a>
<b>Request Types:</b>
<ul>
<li>Events
	<ul>
		<li><a href="#get_evt_detail">Get Event Detail</a>
		<li><a href="#add_evt">Add Event</a>
		<li><a href="#get_evt_talks">Get Talks</a>
	</ul>
<li>Talks
	<ul>
		<li><a href="#get_talk_detail">Get Talk Detail</a>
		<li><a href="#get_talk_comments">Get Talk Comments</a>
	</ul>
<li>Comments
	<ul>
		<li><a href="get_comment_detail">Get Comment Detail</a>
		<li><a href="add_comment">Add Comment</a>
		<li><a href="comment_as_spam">Mark as Spam</a>
	</ul>
<li>User
	<ul>
		<li><a href="#get_user_detail">Get Detail</a>
	</ul>
<li>Site
	<ul>
		<li><a href="#site_status">Status</a>
	</ul>
</ul>

<h2 style="color:#5181C1">Events</h2>
<a name="get_evt_detail"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Event Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get the details for a given event number<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>event_id: integer
	</ul>
<b class="req_title">Example Input Message</b>
<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
<?php echo escape('<request>
        <auth>
                <user>$username</user>
                <pass>$password</pass>
        </auth>
        <action type="getdetail">
                <event_id>1</event_id>
       </action>
</request>'); ?>
</pre>
</div>
<br/>
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
	<a href="#top">[top]</a><br/><br/>
</div>

<a name="add_evt"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Add Event</b>
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
	<a href="#top">[top]</a><br/><br/>
</div>

<a name="get_evt_talks"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Event Talks</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> gettalks<br/>
<b class="req_title">Description:</b> Gets the talks assoiated with an event<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>eid: string, event ID
	</ul>
<b class="req_title">Output:</b>
<ul>
	<li>Multiple, see <a href="#get_talk_detail">Get Talk Detail results</a>
</ul>
	<a href="#top">[top]</a><br/><br/>
</div>

<h2 style="color:#5181C1">Talks</h2>
<a name="get_talk_detail"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Talk Detail</b>
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
	<a href="#top">[top]</a><br/><br/>
</div>

<a name="get_talk_comments"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Talk Comments</b>
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
	<a href="#top">[top]</a><br/><br/>
</div>

<h2 style="color:#5181C1">Comments</h2>
<a name="get_comment_detail"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Comment Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get detail of an event comment with a given ID<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>cid: integer, Comment ID
		<li>rtype: string, Either 'event' or 'talk'
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>title: string, Title of the comment
	</ul>
	<a href="#top">[top]</a><br/><br/>
</div>

<a name="add_comment"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Add Comment</b>
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
		<li>msg: string, either "Comment added!" or error string
	</ul>
	<a href="#top">[top]</a><br/><br/>
</div> 

<a name="comment_is_spam"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Mark as Spam (comment)</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> isspam<br/>
<b class="req_title">Description:</b> Suggest a comment to be spam<br/>
<b class="req_title">Input:</b>
	<ul>
		<li>cid: integer, comment ID number
		<li>rtype: string, either "talk" or "event"
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>msg: string, will always display "Success"
	</ul>
	<a href="#top">[top]</a><br/><br/>
</div>
	<h2 style="color:#5181C1">User</h2>
	<a name="get_user_detail"></a>
	<b class="req_name" style="color:#5181C1;font-size:14px">Get User Detail</b>
	<div style="padding-left:10px">
	<b class="req_title">Action Type:</b> getdetail<br/>
	<b class="req_title">Description:</b> Get detail of a user, given either user ID or username<br/>
	<b class="req_title">Input:</b>
		<ul>
			<li>uid: string, Username/user ID
		</ul>
	<b class="req_title">Output:</b>
		<ul>
			<li>username: string, Joind.in username
			<li>last_login: string, User's last login time (unix timestamp)'
			<li>ID: integer, user's ID'
			<li>full_name: string, User's full name'
		</ul>
		<a href="#top">[top]</a><br/><br/>
	</div>

	<a name="validate_user"></a>
	<b class="req_name" style="color:#5181C1;font-size:14px">Validate User</b>
	<div style="padding-left:10px">
	<b class="req_title">Action Type:</b> validate<br/>
	<b class="req_title">Description:</b> Check login/password to check login<br/>
	<b class="req_title">Input:</b>
		<ul>
			<li>uid: string, Username/user ID
			<li>pass: string, MD5 hased value of password
		</ul>
	<b class="req_title">Output:</b>
		<ul>
			<li>success: string, Status of login verification (string of 'success' if info is good)
		</ul>
		<a href="#top">[top]</a><br/><br/>
	</div>
	
<h2 style="color:#5181C1">Site</h2>
<a name="site_status"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Status</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> status<br/>
<b class="req_title">Description:</b> Get site's current statusbr/>
<b class="req_title">Input:</b>
	<ul>
		<li>test_string: [optional] send in a string, get the same string back
	</ul>
<b class="req_title">Output:</b>
	<ul>
		<li>test_string: [optional] send in a string, get the same string back
		<li>dt: RFC 2822 formatted date
	</ul>
	<a href="#top">[top]</a><br/><br/>
</div>