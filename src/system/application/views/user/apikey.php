<h1>API Access Keys</h1>
<?php
$this->load->view('user/_nav_sidebar', array('pending_events'=>array(),
                                            'event_claims'=>array()));
?>
<?php
if(isset($grants) && is_array($grants)) {
?>
<p>These are the applications that you have granted access to.  You can revoke their access here</p>

<?php

    echo "<ul>";
    foreach($grants as $access) {
        echo "<li>" . $access->application . ", (last used " . $access->last_used_date . ") <a href=\"/user/revoke_access?id=" . $access->id . "\" onclick=\"notifications.prompt('are you sure?  This application will no longer recognise your joind.in account', function(){window.location='/user/revoke_access?id=" . $access->id . "';}, function(){return false;}); return false;\" >revoke permissions</a>";
        echo "</li>";
    }
    echo "</ul>";
}

if(isset($keys) && is_array($keys)) {
?>
    <h2>Your API Keys</h2>

<p>You can obtain a <b>key</b> and <b>secret</b> to use with the Joind.in web API if you wish to create a client to consume the data.  Many activities are publicly accessible, however to post comments or perform other actions which change our data, you will need to identify yourself using OAuth.  Please note that the secret should never be shared in any way (e.g. included in a public code repository).</p>

<p>You can find out more by <a href="http://joindin.github.io/joindin-api/">reading the API documentation</a></p><br />

<?php
    echo "<ul>";
    foreach($keys as $key) {
        echo "<li>" . $key->application . " <a href=\"/user/apikey_delete?id=" . $key->id . "\" onclick=\"notifications.prompt('are you sure?', function(){window.location='/user/apikey_delete?id=" . $key->id . "';}, function(){return false;}); return false;\" >delete this key</a>";
        echo "<ul><li>Key: " . $key->consumer_key . "</li>";
        echo "<li>Secret: " . $key->consumer_secret. "</li>";
        echo "<li>Callback URL: " . $key->callback_url . "</li></ul>";
        echo "</li>";
    }
    echo "</ul>";
} 
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
<p>Callback URL: <input type="text" name="callback_url" /><br />
The callback URL is the location that users should be returned to after they have granted authorisation.  Device-specific URLs can be used here.  Users can only authorise this application when they arrive with this callback URL specified.</p>
<input type="submit" value="Request API Keys" />
<?php
echo form_close();


