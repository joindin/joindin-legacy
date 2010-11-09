
<h2>Manage User Account</h2>

<h3>What are User Accounts?</h3>
<p>
The answer to this one is pretty simple, but we wanted to be sure you knew all of the places that your user account
could touch. Not only are the user accounts what tells the site who is who, but it also connects you with the
comments, claims and content you might produce on the site. If you're logged in and leave any kind of comment, your
user informaton is linked to it.
</p>
<p>
If you get lost, you can always get back to your main "user homepage" by clicking on the "Account" link in the top right hand
portion of the screen.
</p>

<h3>User Dashboard</h3>
<p>
The user dashboard is the first thing you'll see when you log in or when you click the "Account" link in the top 
right-hand part of the page. You'll notice two distinct sections of the page that, depending on if you're a frequent 
speaker or commentor, may or may not be filled in:
<ul>
<li><b>My Talks</b><br/>
If you're a speaker or presenter, these are the talks that you've given at one time or another. They're listed by the
talk name and by when they were given. You can see at a glance what the ratings were for the session and how many 
comments were left.
<li><b>My Comments</b><br/>
This section links you back to the comments you've made on either events or on talks/sessions. It's a list of the links 
to the comments themselves so you can go back and review your thoughts.
</ul>
</p>

<h3>Manage Account</h3>
<p>
Once you're logged into your account, you'll see a "Manage Account" tab in the left-hand side of the page. Clicking
on this gives you a form where you can update some of your personal information. You can change your:
<ul>
<li>Full name
<li>Email address
<li>Password (and confirmation)
</ul>
</p>
<p>
Changing this information is instant and will apply as soon as you click the "Save changes" button.
</p>

<h3>Where does my user icon come from?</h3>
<p>
On several pages of the site, like the user profiles and the talk details, you'll notice pictures of the user (or just of something they like). These images are called "gravatars" and are pulled directly from the <a href="http://gravatar.com"">gravatar website</a>. The images are linked to your Joind.in account by email address, so be sure that your Joind.in email account matches the Gravatar profile you want to use. Images are currently refreshed <b>1 day</b> from the last time they were checked, so if you update your profile image on the Gravatar site, it will take a bit to change here.
</p>

<h3>Speaker Profile</h3>
<p>
If you're a speaker at one or more event, you might want to define your speaker profile. This can define a bit more 
information about you as a person than just the normal user profile (which only provides name and email address). Speaker 
profiles can be pulled from the site (via the API) through a profile access key.
</p>
<p>
To get started using the user profiles, you'll need to click on the "Speaker Profile" link on the right-hand side of the page 
(in the Navigation box) and click to create a profile. You can add as much or as little information as you might want and
save it. You'll be taken back to a page with a summary of the speaker information for you to ensure everything's set correctly.
To set up an access code, you'll need to click on the "Profile Access" tab and add a level of access. Check off the data you'd 
like to include in the profile and hit "Save Changes". This will go back to the main list including the code you'll need to give
to the requesting party.
</p>

<a name="tokens"></a>
<h3>Tokens</h3>
<p>
Tokens go hand in hand with <b>Speaker Profiles</b>. They're how you name the different access profiles so you can have a 
quick link to the ones you want to share. For example, say you've already set up your Speaker Profile and want to 
start sharing that information with the world. You can click on the <b>Profile Access</b> tab in the speaker profile management
section and make a new access profile. You'll see a spot for a "Token Name" and a "Token Descripton". 
</p>
<?php
$msg="<b>NOTE:</b> Token names can only be made up of letters and numbers";
$this->load->view('msg_info', array('msg' => $msg)); ?>
<p>
For our example, you want to only share your full name and blog address for this token, so you click the checkboxes next to 
those two and put "nameblog" in the token name field. The description might be something like "sharing full name and blog 
only". When you save that, the token "nameblog" will always point to that access profile. You can then give this token to 
anyone out there and the only thing they'll ever see is your full name and blog, nothing else.
</p>



