<?php 
menu_pagetitle('Joind.In Data Import');
?>
<h1>Importing Talks to Your Event</h1>

<p>We know that you have so many other things to take care of when you organise an event, and that you are probably publishing your schedule in a number of places, so we've created a CSV import to let you quickly get your existing data into joind.in (data entry is nobody's idea of fun!).  To get started, click on the "Import Event Info" on the right hand event bar:</p>

<img src="/inc/img/docs/screenshot1.png" /><br/>

<p>You will go to a page which gives full details of how to create a file to import, this usually involves using a spreadsheet with one row per talk/session and then saving the file as a CSV (comma-separated values) file from your spreadsheet program (Excel, OpenOffice or Google Docs can all do this).</p>

<h3 style="color:#5181C1">Preparing Data for Import</h3>

<p>The basic CSV data format is very simple, it looks something like the picture below - this is the actual data used to populate the <a href="/phpnw10">PHPNW10 event</a>:</p>

<img src="/inc/img/docs/csv1.png" /><br /><br />

<p>The first row contains titles - these can appear in any order but must be spelled as shown</p>

<img src="/inc/img/docs/csv2.png" /><br /><br />

<p>Look out for the dates and time being separate and formatted quite strictly.  You can enter the date in your spreadsheet in any format, then highlight the column and choose "format cells" to change how the data is actually shown.  The same settings are then used when you create the CSV file.</p>

<img src="/inc/img/docs/csv3.png" /><br /><br />

<p>The last thing to mention is that if you specify a track for a talk, this track must already have been created for your event (do this from the main event page - see the admin links on the right hand bar again).  The import will run until it hits a track it doesn't recognise, and will then stop at this point, so you may need to add a track, remove the already-imported rows from your spreadsheet, and try again!  Check that the track names are spelled exactly the same on the spreadsheet as they are in the event on joind.in</p>

