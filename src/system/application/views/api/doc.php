<?php menu_pagetitle('API Documentation'); ?>

<style>
b.req_title { color: #767676; }
b.req_name { font-size: 12px; }
</style>

<h1 style="margin-top:0px;margin-bottom:2px;color:#B86F09"><?php echo $this->config->item('site_name'); ?> API</h1>

<p><b>There is a new API under development.  <a href="/api/v2docs">click here</a> to find out more about the replacement RESTful API.  Both APIs are currently supported</b></p>

<p>In true PHP style, we have more than one API:
<ul><li>The old v1 API is RPC-ish and isn't actively developed.  See the <a href="/api/v1docs">API v1 docs</a></li>
<li>The v2 API is RESTful, user-friendly and more robust.  We also have funky interactive documentation.  <a href="/api/v2docs">Find out about the v2 API</a></li></ul>
</p>
