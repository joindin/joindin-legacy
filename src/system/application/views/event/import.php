<h2>Import Talks</h2>
<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
<?php $this->load->view('msg_error', array('msg' => $error_msg)); ?>
<?php endif; ?>

<?php echo form_open_multipart('event/import/'.$details[0]->ID); ?>
<div class="row">
    <p>
    Joind.in offers this form to allow you to quickly and easily upload the details of the sessions at your event.  The file
    must be in CSV format, which can be exported from Microsoft Excel.  The first row of the file may include the following
    columns, bold type indicates a required field:<ul>
        <li><b>Title</b></li>
        <li><b>Description</b></li>
        <li><b>Speaker</b> (separate multiple speakers with commas)</li>
        <li><b>Date</b> (in the format YYYY-MM-DD, your spreadsheet program should have an option for formatting dates this way)</li>
        <li><b>Time</b> (in the format HH:MM)</li>
        <li>Duration (in minutes)</li>
        <li>Language (Two-character language code.  Currently supported: fr, us, de, it, sp, ge, uk, porb, pl, fi.  Default is uk)</li>
        <li>Type (this can be one of "Talk", "Keynote", "Workshop", "Social")</li>
        <li>Track (separate multiple tracks with commas)</li></ul>
    </p>

    <p><b>Notes:</b> The columns can appear in any order.  The event <b>timezone</b> must be configured correctly before you import.
    The <b>tracks</b> must be created through the event detail page before the import is run (these can be renamed later, just create
    the same tracks as you have names in your import).  If you are presented with options on export, fields should be delimited with
    a comma, and enclosed with a double quote (").</p>

    <p>If you have any problems, questions or comments then let us know!  You can contact us at <a href="mailto: feedback@joind.in">feedback@joind.in
    </a> - if there are problems with your upload then feel free to send us the CSV file you are using to import along with any
    error messages.</p>
</div>
<div class="row">
    <p>Import data for <a href="/event/view/<?php echo $details[0]->ID; ?>"><b><?php echo $details[0]->event_name; ?></b></a></p>

    <label for="event_end">Select file to upload:</label>
    <?php 
        $attr=array(
            'name'	=>'xml_file',
            'id'	=>'xml_file'
        );
        echo form_upload($attr); 
    ?>
</div>
<div class="row">
    <?php echo form_submit('sub','Upload'); ?>
</div>

<?php echo form_close();
