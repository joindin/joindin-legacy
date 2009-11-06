<h2>Importing Event Information</h2>

<p>
Joind.in allows you to import your event's information via an XML file 
making it easier to add all at once. See below for and example of the XML
format to follow to ensure your sessions get imported correctly.
</p>
<p>
The XML file's contents and format will be validated for correctness.
</p>

<div style="padding:3px;border:1px solid #000000;background-color:#F8F8F8">
<pre>

&lt;?xml version="1.0" encoding="UTF-8"?>
&lt;event> 
  &lt;event_title>Sample Event #1</event_title>
  &lt;event_start_date>2009-02-10T08:00:21+00:00</event_start_date>
  &lt;event_end_date>2009-02-14T17:00:21+00:00</event_end_date>
  &lt;event_desc>
    This is my sample event...
  &lt;/event_desc>
  &lt;sessions>
    &lt;session>
      &lt;session_title>Sample Session #1&lt;/session_title>
      &lt;session_start>2009-02-12T15:00:21+00:00&lt;/session_start>
      &lt;session_end>2009-02-12T16:00:21+00:00&lt;/session_end>
      &lt;session_desc>
	    This is a sample session #1 by Cal
      &lt;/session_desc>
      &lt;session_speaker>Cal Evans&lt;/session_speaker>
    &lt;/session>
    &lt;session>
      &lt;session_title>Sample Session #2&lt;/session_title>
      &lt;session_start>2009-02-12T15:00:21+00:00&lt;/session_start>
      &lt;session_end>2009-02-12T16:00:21+00:00&lt;/session_end>
      &lt;session_desc>
	    This is a sample session #2
      &lt;/session_desc>
      &lt;session_speaker>Chris Shiflett&lt;/session_speaker>
    &lt;/session>
  &lt;/sessions>
&lt;/event>
</pre>
</div>