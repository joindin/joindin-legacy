<h2>Importing Event Information</h2>

<p>
<?php echo $this->config->item('site_name'); ?> allows you to import your event's sessions via an XML file
making it easier to add all at once. See below for and example of the XML
format to follow to ensure your sessions get imported correctly.
</p>
<p>
The XML file's contents and format will be validated for correctness.
</p>
<p><b>Tips:</b></p>
<ul>
<li>If there are multiple speakers on the same session, seperate their names with commas</li>
<li>The session_start time should be the correct unix timestamp for the 
timezone where the event takes place - you can change this in the event itself.<li>
<li>session_track is optional and should match (case-sensitively) the track name already
created in the event itself</li>
<li>session_type is optional and should be one of "Talk", "Keynote", "Social", "Workshop",
and "Event Related" (case sensitive matching)</li>
</ul>

<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>

&lt;event&gt;
  &lt;sessions&gt;
  &lt;session&gt;
	&lt;session_start&gt;
	  1276155000
	&lt;/session_start&gt;
	&lt;session_type&gt;
	  Workshop
	&lt;/session_type&gt;
	&lt;session_speakers&gt;
	  &lt;speaker&gt;Sebastian Bergmann&lt/speaker&gt;
	&lt;/session_speakers&gt;
	&lt;session_title&gt;
	  Quality Assurance in PHP Projects
	&lt;/session_title&gt;
	&lt;session_desc&gt;
	  When things go wrong in software projects, the team has to work 
	  overtime and cancel vacations. More often than not, deadlines and 
	  quality goals are missed nevertheless. Because software usually 
	  lives longer than originally planned, the real problems crop up 
	  when changes and extensions become necessary later on.  In this 
	  tutorial, Sebastian Bergmann, a pioneer in the field of quality 
	  assurance in PHP projects and creator of PHPUnit, imparts 
	  comprehensive knowledge and experience about testing and quality 
	  assurance in Web projects.  Using examples from the PHP world, 
	  the tutorial elucidates the planning, execution, and automation 
	  of tests for the different layers and tiers of a Web software 
	  architecture, the measuring of software quality by means of 
	  software metrics, as well as establishing successful development 
	  processes and methods such as continuous integration.
	&lt;/session_desc&gt;
  &lt;/session&gt;
  &lt;session&gt;
	&lt;session_start&gt;
	  1276249500
	&lt;/session_start&gt;
	&lt;session_type&gt;
	  Talk
	&lt;/session_type&gt;
	&lt;session_track&gt;
	  Track 3
	&lt;/session_track&gt;
	&lt;session_speakers&gt;
	  &lt;speaker&gt;Rob Allen&lt;/speaker&gt;
	  &lt;speaker&gt;Keith Casey&lt;/speaker&gt;
	&lt;/session_speakers&gt;
	&lt;session_title&gt;
	  Zend Framework on Windows
	&lt;/session_title&gt;
	&lt;session_desc&gt;
	  Following the recent investment of time and effort by both Microsoft 
	  and the core PHP developers, Windows with PHP 5.3 is a good platform choice. 
	  This session will take a look at the parts of this ecosystem, walking 
	  through the installation of PHP on Windows and showing how to use the 
	  FastCGI component of IIS and the URL rewrite module. With SQL Server 
	  accessed via Zend_Db and the ZF command line tools available on Windows, 
	  we'll show how we can use the best tools of PHP to build an application 
	  on this platform.
	&lt;/session_desc&gt;
  &lt;/session&gt;
</pre>
</div>
