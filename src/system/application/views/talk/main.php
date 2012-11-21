<?php 
menu_pagetitle('Talks');
?>
<?php
//echo '<pre>'; print_r($talks['recent']); echo '</pre>';
?>
<h1 class="icon-talk">Talks</h1>

<p class="filter">
    <a href="#pop_recent">Recently Popular</a> |
    <a href="#pop_all">Most Popular (All Time)</a>
</p>

<h2>Recent Popular Talks</h2>
<a name="pop_recent"></a>
<?php
foreach (array_slice($talks['recent'],0,10) as $v) {
    $this->load->view('talk/_talk-row', array('talk'=>$v));	 }
?>
<br/><br/>

<h2>Popular Talks (All Time)</h2>
<a name="pop_all"></a>
<?php
foreach (array_slice($talks['popular'],0,10) as $v) {
    $this->load->view('talk/_talk-row', array('talk'=>$v));	 }
//$this->load->view('talk/_popular-speaker', array('talks'=>$talks['popular']));	
