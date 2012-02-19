<h1>API Keys</h1>

<p>On this page you can obtain a <b>key</b> and <b>secret</b> to use with the Joind.in web API if you wish to create a client to consume the data.  Many activities are publicly accessible, however to post comments or perform other actions which change our data, you will need to identify yourself using OAuth.  Please note that the secret should never be shared in any way (e.g. included in a public code repository).</p>

<p>You can find out more by <a href="/api/v2docs">reading the API documentation</a></p><br />

<?php

if(isset($keys) && is_array($keys)) {
?>
    <h2>Your Existing Keys</h2>
<?php
    echo "<ul>";
    foreach($keys as $key) {
        echo "<li>" . $key->application;
        echo "<ul><li>" . $key->consumer_key . "</li>";
        echo "<li>" . $key->consumer_secret. "</li></ul>";
        echo "</li>";
    }
    echo "</ul>";
} else {
?>
<p>To obtain a new consumer key and secret to use with the Joind.in API, please fill in the form below:</p>
        <?php if (!empty($this->validation->error_string)): ?>
<div class="box">
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
</div>
        <?php endif; ?>
<?php echo form_open('user/apikey', array('class' => 'form-oauth')); ?>
<p>Application display name: <input type="text" name="application" /></p>
<p>What does your application do? <textarea name="description"></textarea></p>
<input type="submit" value="Request API Keys" />
<?php
echo form_close();
}

?>

