<?php 
menu_pagetitle('About');
?>
<h1 class="icon-about">About</h1>

<?php 
$msg='<a href="/about/iphone_support"><img src="/inc/img/iphone.jpg" height="70" style="margin-right:10px" 
	align="left" border="0"/></a><b style="font-size:13px">Check out our 
	new iPhone application!</b><br/>Want the best way to keep up with all of the events and comments from 
	' . $this->config->item('site_name') . ' directly from your phone? <a href="http://itunes.apple.com/us/app/joind-in/id355184913?mt=8">Download
	our iPhone app here!</a><br/><a href="/about/iphone_support">Visit our App Support page</a>';
$this->load->view('msg_info', array('msg' => $msg));

$msg='<b>New to ' . $this->config->item('site_name') . '?</b> Check out our <a href="/inc/files/User_Guide.pdf">User Guide</a> for the ins and outs
of using ' . $this->config->item('site_name') . '!';
$this->load->view('msg_info', array('msg' => $msg));

$msg='<b>We have an open API!</b> Looking for more information on how you can connect your applications to the 
	' . $this->config->item('site_name') . ' API? <a href="/api">Check out the docs here!</a>';
$this->load->view('msg_info', array('msg' => $msg)); 

$msg='<b>We Have Widgets!</b> Want to put a little bit of '.$this->config->item('site_name').'into your site? Check out <a href="/about/widget">the widget info here!</a>';
$this->load->view('msg_info', array('msg' => $msg)); 

?><br/>

<h3 style="color:#5181C1">Like the talk? Let 'em know!</h3>
<p>
<?php echo $this->config->item('site_name'); ?> provides the missing link between the people attending a conference and the ones that presented.
The usual method of handing out paper forms is outdated and needs to be replaced. That's where we come in - 
attendees can post their comments directly to each of the talks they attended, giving the speaker direct feedback
on how they did and what they can do to improve.
</p>
<p>
<?php echo $this->config->item('site_name'); ?> also has something to offer the speakers - you can track your record across the conferences and see
how changes in your talk might have made a difference in your ratings.
</p>

<br/>
Like the site or just want to give us some suggestions? <a href="/about/contact">Drop us a note!</a>

<br/><br/><br/>
<h3 style="color:#5181C1">How can <?php echo $this->config->item('site_name'); ?> work for you?</h3>
<p>
Are you organizing a conference and wondering how you can get good feedback from those attending? <?php echo $this->config->item('site_name'); ?> lets
the attendees of your event leave the feedback they want (public or private) directly to the speakers and those
organizing the event. Real reviews like this can give you a better idea of how good the event was and things
you could do better the next time around.
</p>
<p>
<?php echo $this->config->item('site_name'); ?> can also be a resource to find out what people are looking for in conferences and other events. You can
see which of the talks were rated the highest and who the speakers were on those talks. Attendees also have the 
option of leaving feedback or making suggestions on the event as a whole.
</p>
<p>
If you're looking for a good way to get some good, honest feedback about your event, you can either 
<a href="/event/submit">submit your event</a> or <a href="/about/contact">contact us directly</a> about how <?php echo $this->config->item('site_name'); ?>
can help.
</p>

<br/>
<h3 style="color:#5181C1">Other Information</h3>
<ul>
<li><a href="/about/import">Importing Event Information</a> (XML)
<li><a href="/about/evt_admin">Event Admin Cheat Sheet</a>
</ul>
