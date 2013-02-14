<?php menu_pagetitle('API Documentation'); ?>

<style>
b.req_title { color: #767676; }
b.req_name { font-size: 12px; }
</style>

<h1 style="margin-top:0px;margin-bottom:2px;color:#B86F09"><?php echo $this->config->item('site_name'); ?> API</h1>

<p><b>This is the older API version for joind.in; <a href="/api/v2docs">click here</a> to find out more about the replacement RESTful API</b></p>


<p>
The <?php echo $this->config->item('site_name'); ?> API allows for the fetching and updating of information in the service's database. You can use both XML and JSON messaging to communicate with it. Here's an XML-based example structure each request should follow:
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
<br/>
or, if you reprefer JSON: 
<br/><br/>

<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>
{"request":{
    "auth":{
        "user":"your_username","pass":"your_password"
    },
    "action":{
        "type":"status",
        "data":{"test_string":"my test"}
    }
}}
</pre>
</div>

<p>
In our above examples, you can see the <b>"auth"</b> section where you would replace $username and $password with your login information - the auth section only needs to be included where authentication is required for that action. The password in the auth section should be md5. Below that there's the <b>"action"</b> section which indicates which operation the system should perform. In this example we're making a "getdetail" call to grab the information for the given event ID.
</p>
<p>
<?php $msg='<b>Please note:</b> be sure to send a "Content-Type" header along with your request to ensure the service parses the message correctly. By default, it will assume the message is XML formatted.</p>';
$this->load->view('msg_info', array('msg' => $msg)); ?>
<br/><br/>
<h3>Types</h3>
<p>
There are four different URLs you can make requests to:
<ul>
    <li><b>api/site</b> - to get the current status of the web service
    <li><b>api/event</b> - to get information on events
    <li><b>api/talk</b> - to get information on talks
    <li><b>api/user</b> - to get information on users
    <li><b>api/comment</b> - to get information about individual comments
</ul>
</p>

<p>
Our sample XML above would need to be sent to "<?php echo $this->config->site_url(); ?>api/event" to work correctly. If you send it to an incorrect URL you probably won't get quite what you're expecting.
</p>

<h3>Responses</h3>
<p>
By default, responses will be made in an JSON format. There is an optional attribute you can add to the "action" tag in your request called <b>"output"</b>. This is set to "json" initially but it can also be set to "xml" if you prefer your response in that format. Here's an example of the XML output:
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
        <li><a href="#get_evt_talks">Get Talks</a>
        <li><a href="#get_evt_list">Get Event Listing</a>
        <li><a href="#evt_attend">Attend Event</a>
        <li><a href="#add_evt_comment">Add Comment</a>
        <li><a href="#get_evt_comment">Get Event Comments</a>
        <li><a href="#get_evt_talk_comment">Get Event Talk_Comments</a>
        <li><a href="#add_evt_track">Add Event Track</a>
    </ul>
<li>Talks
    <ul>
        <li><a href="#get_talk_detail">Get Talk Detail</a>
        <li><a href="#get_talk_comments">Get Talk Comments</a>
        <li><a href="#claim_talk">Claim</a>
        <li><a href="#add_comment">Add Comment</a>
    </ul>
<li>Comment
    <ul>
        <li><a href="#get_comment_detail">Get Comment Detail</a>
        <li><a href="#comment_as_spam">Mark as Spam</a>
    </ul>
<li>User
    <ul>
        <li><a href="#get_user_detail">Get Detail</a>
        <li><a href="#get_user_comments">Get User Comments</a>
        <li><a href="#validate_user">Validate User</a>
    </ul>
<li>Site
    <ul>
        <li><a href="#site_status">Status</a>
    </ul>
</ul>

<h2 style="color:#5181C1">Events (/api/event)</h2>
<a name="get_evt_detail"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Event Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get the details for a given event number<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>event_id: integer
    </ul>
<br/>
<b class="req_title">Output:</b> An array containing a single object.  The object has the following properties:
    <ul>
        <li>event_name: string, Name of the event
        <li>event_start: Unix timestamp
        <li>event_end: Unix timestamp
        <li>event_lat: number, Latitude of the venue location
        <li>event_long: number, Longitude of the venue location
        <li>ID: integer, ID for the event
        <li>event_loc: string, Event location
        <li>event_desc: string, Event description
        <li>active: integer, Whether the event is active or not
        <li>event_stub: string, Stub/shortcut value for event
        <li>event_icon: string, path to image icon
        <li>pending: integer, whether the event is awaiting approval
        <li>event_hashtag: string, twitter/blogging hashtag for this event
        <li>event_href: string, event homepage
        <li>event_cfp_start: unix timestamp, date the call for papers opens for this event
        <li>event_cfp_end: unix timestamp, date the call for papers closes for this event
        <li>event_voting: integer, whether users can vote on the sessions (currently not in use)
        <li>private: integer, whether this event is only visible to its members
        <li>event_tz_cont: string, Continent part of timezone name (e.g. 'Europe')
        <li>event_tz_place: string, Detial part of timezone name (e.g. 'London')
        <li>allow_comments: integer, Whether comments are accepted on this talk (1 for yes, 0 for no)
        <li>num_attend: integer, number of people marked as attending
        <li>num_comments: integer, the number of comments on this event
        <li>user_attending: integer, whether the current user is attending this event
        <li>now: string, either "now" if the event is now on or "" if it isn't (not present in event/getlist)
        <li>tracks: array, List of track objects associated with this session.  Track properties are track_name, ID, track_color used (1 or 0, whether there are sessions in it or not), event_id and track_desc (description) (not present in event/getlist)
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="get_evt_talks"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Event Talks</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> gettalks<br/>
<b class="req_title">Description:</b> Gets the talks assoiated with an event<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>event_id: string, event ID
    </ul>
<b class="req_title">Output:</b> An array of Objects.  Each object has the following properties:
    <ul>
        <li>talk_title: string, Title of the talk
        <li>speaker: string, Speaker of the talk
        <li>slides_link: For future use
        <li>date_given: integer, Unix timestamp for the date talk presented
        <li>event_id: integer, The event ID the talk belongs to
        <li>ID: integer, The talk ID number
        <li>talk_desc: string, Description of the talk
        <li>event_tz_cont: string, Continent part of timezone name (e.g. 'Europe')
        <li>event_tz_place: string, Detial part of timezone name (e.g. 'London')
        <li>lang: string, Language the talk given in (2-digit short code)
        <li>rank: integer, Average rating of comments on selected talk
        <li>tcid: string, Type of entry (e.g."Talk")
        <li>comment_count: integer, Number of comments on this session
        <li>now_next: string, either "now" if the talk is now on, "next" if it is on next, or "" otherwise. NOTE the logic behind this is *very* crude
        <li>tracks: array, List of track objects associated with this session.  Track properties are track_name, ID, track_color and track_desc (description)
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="get_evt_list"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Event Listing</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getlist<br/>
<b class="req_title">Description:</b> Gets the event listing for various types<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>event_type: string, event type [hot, upcoming, past]
    </ul>
<b class="req_title">Output:</b>
<ul>
    <li>Multiple, see <a href="#get_evt_detail">Get Event Detail results</a>
</ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="add_evt_comment"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Add Comment</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> addcomment<br/>
<b class="req_title">Description:</b> Add a comment to the event<br/>
<b class="req_title">Authentication:</b> not required (but user name used with comment if supplied)<br />
<b class="req_title">Input:</b>
    <ul>
        <li>event_id: integer, id of the event to add the comment to
        <li>comment: string, comments to submit
        <li>source: string, optional source application of comment (defaults to: api)
    </ul>
<b class="req_title">Output:</b>
    <ul>
        <li>msg: string, either "comment added!" or error string
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="evt_attend"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Attend Event</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> attend <br/>
<b class="req_title">Description:</b> Marks this user as attending the event<br/>
<b class="req_title">Authentication:</b> required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>eid: integer, id of the talk to add the comment to
    </ul>
    <b class="req_title">Output:</b>
    <ul>
        <li>msg: Either success or one of a few failure messages
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="get_evt_comments"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Event Comments</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getcomments<br/>
<b class="req_title">Description:</b> Get all comments associated with an event<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>event_id: integer, ID number of event to get comments for
    </ul>
<b class="req_title">Output:</b> An array of values containing the following for each comment
    <ul>
        <li>event_id: integer, ID number of the event comment is on
        <li>comment: string, Comments from the user
        <li>date_made: integer, Unix timestamp of when comment was posted
        <li>user_id: integer, If a registered user made the comment, a non-zero value is here
        <li>active: integer, If the comment is marked as active or not
        <li>ID: integer, ID number of the comment
        <li>cname: string, If a registered user made the comment, their username is here
        <li>private: integer, If the comment is marked as private or not
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="get_evt_talk_comments"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Event Talk Comments</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> gettalkcomments<br/>
<b class="req_title">Description:</b> Get all comments associated with sessions at an event.  Private comments are not shown, 
results are returned in date order with newest first.<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>event_id: integer, ID number of event to get comments for
    </ul>
<b class="req_title">Output:</b> An array of values containing the following for each comment
    <ul>
        <li>talk_title: string, Title of the talk
        <li>speaker: string, Speaker of the talk
        <li>date_given: integer, Unix timestamp for the date talk presented
        <li>date_made: integer, Unix timestamp of when comment was posted
        <li>rating: integer, The rating the user gave to this talk
        <li>comment: string, Comments from the user
        <li>full_name: string, If a registered user made the comment, their username is here
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="add_evt_track"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Add Event Track</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> addtrack<br/>
<b class="req_title">Description:</b> Add a track to an existing event<br/>
<b class="req_title">Authentication:</b> valid login, admin for event<br />
<b class="req_title">Input:</b>
    <ul>
        <li>event_id: integer, ID number of event to get comments for
        <li>track_name: string, name for the track
        <li>track_desc: string, description for the track
    </ul>
<b class="req_title">Output:</b> An array of values containing the following for each comment
    <ul>
        <li>msg: Either success or one of a few failure messages indicating the problem
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<h2 style="color:#5181C1">Talks (/api/talk)</h2>
<a name="get_talk_detail"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Talk Detail</b>
<div style="padding-left:10px">
    <p>
        <b>Note:</b> if the event the session belongs to is marked as private, user credentials must be included with the 
        detail request to check for invite status to the event.
    </p>
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get the details for given talk number<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>talk_id: integer, ID number of the talk to fetch
    </ul>
<b class="req_title">Output:</b> An array containing a single object.  Object properties are:
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
        <li>event_tz: Deprecated
        <li>event_tz_cont: string, Continent part of timezone name (e.g. 'Europe')
        <li>event_tz_place: string, Detail part of timezone name (e.g. 'London')
        <li>tavg: integer, Average rating of comments on selected talk
        <li>tcid: string, Type of entry ("Talk")
        <li>event_id: integer, The event ID (same as eid)
        <li>ID: integer, The talk ID number (same as tid)
        <li>active: integer, Whether this talk is in use 
        <li>owner_id: empty, not used
        <li>event_voting: integer, Whether voting is active for thi event (currently not in use)
        <li>private: integer, If this session is only visible to members
        <li>lang_abbr: string, Short code for the session language
        <li>ccount: integer, Number of comments on this session
        <li>last_comment_date: unix timestamp, Date of the last comment added to this session
        <li>allow_comments: integer, Whether comments are accepted on this talk
        <li>now_next: string, either "now" if the talk is now on, "next" if it is on next, or "" otherwise. NOTE the logic behind this is *very* crude
        <li>tracks: array, List of track objects associated with this session.  Track properties are track_name, ID, track_color and track_desc (description)
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="get_talk_comments"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Talk Comments</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getcomments<br/>
<b class="req_title">Description:</b> Get all comments associated with a talk<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>talk_id: integer, ID number of talk to get comments for
    </ul>
<b class="req_title">Output:</b> An array of values containing the following for each comment
    <ul>
        <li>talk_id: integer, ID number of the talk comment is on
        <li>rating: integer, The rating the user gave to this talk
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

<a name="add_comment"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Add Comment</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> addcomment<br/>
<b class="req_title">Description:</b> Add a comment to a given talk<br/>
<b class="req_title">Authentication:</b> required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>talk_id: integer, id of the talk to add the comment to
        <li>rating: integer, rating to give the talk (range of 1-5)
        <li>comment: string, comments to submit
        <li>private: integer, whether to make the comment private or not
        <li>source: string, optional source application of comment (defaults to: api)
    </ul>
<b class="req_title">Output:</b>
    <ul>
        <li>msg: string or array, either "comment added!" or error string (or array of strings)
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>
    
<a name="claim_talk"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Claim Talk</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> claim<br/>
<b class="req_title">Description:</b> Send claim request for talk ID<br/>
<b class="req_title">Authentication:</b> required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>talk_id: integer, ID number of talk to submit claim for
    </ul>
<b class="req_title">Output:</b> Failure/Success message
    <ul>
        <li>msg: Either success or one of a few failure messages
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<h2 style="color:#5181C1">Comments (/api/comments)</h2>
<a name="get_comment_detail"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get Comment Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get detail of an event comment with a given ID<br/>
<b class="req_title">Authentication:</b> not required<br />
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

<a name="comment_is_spam"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Mark as Spam (comment)</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> isspam<br/>
<b class="req_title">Description:</b> Suggest a comment to be spam<br/>
<b class="req_title">Authentication:</b> not required<br />
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

<h2 style="color:#5181C1">User (/api/user)</h2>
<a name="get_user_detail"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get User Detail</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getdetail<br/>
<b class="req_title">Description:</b> Get detail of a user, given the user ID<br/>
<b class="req_title">Authentication:</b> required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>uid: string, user ID
    </ul>
<b class="req_title">Output:</b>
    <ul>
        <li>username: string, <?php echo $this->config->item('site_name'); ?> username
        <li>last_login: string, User's last login time (unix timestamp)
        <li>ID: integer, user ID
        <li>full_name: string, User's full name
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="get_user_comments"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Get User Comments</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> getcomments<br/>
<b class="req_title">Description:</b> Get the user's talk and event comments<br/>
<b class="req_title">Authentication:</b> not required<br />
<b class="req_title">Input:</b>
    <ul>
        <li>username: string, Username
        <li>type: [optional] string, type of comments to get (event or talk)
    </ul>
<b class="req_title">Output:</b>
    <ul>
        <li>Multiple records of:
            <ul>
                <li>talk_id/event_id: integer, ID number of the talk or event
                <li>comment: string, User's comment
                <li>date_made: integer, Time comment was made (unix timestamp)
                <li>user_id: integer, User ID number of user that made the post
                <li>active: integer, Current status of comment (1 = active, 0 = inactive)
                <li>ID: integer, ID number of the comment
                <li>type: string, Type of comment (event or talk)<br/><br/>
                <li>rating: integer, [In talk data only] Rating
                <li>comment_type: string, [In talk data only] (null for normal comments, "vote" for a pre-event vote)<br/><br/>
                <li>cname: string, [In event data only] Commentor's full name
            </ul>
    </ul>
    <a href="#top">[top]</a><br/><br/>
</div>

<a name="validate_user"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Validate User</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> validate<br/>
<b class="req_title">Description:</b> Check login/password to check login<br/>
<b class="req_title">Authentication:</b> not required<br />
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

<h2 style="color:#5181C1">Site (/api/site)</h2>
<a name="site_status"></a>
<b class="req_name" style="color:#5181C1;font-size:14px">Status</b>
<div style="padding-left:10px">
<b class="req_title">Action Type:</b> status<br/>
<b class="req_title">Description:</b> Get site's current status<br/>
<b class="req_title">Authentication:</b> not required<br />
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
