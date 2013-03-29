<?php
if (!empty($results)) {
   menu_pagetitle('Search for: ' . escape($this->validation->search_term));
} else {
    menu_pagetitle('Search');
}
?>
<h1 class="icon-search">Search</h1>

<div class="box">
    <?php echo form_open('/search'); ?>

    <?php if (!empty($this->validation->error_string)): ?>
        <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>

    <div class="row">
        <label for="search_term">Search term</label>
        <?php
            $arr=array(
                'name'	=> 'search_term',
                'id'	=> 'search_term',
                'size'	=> 50,
                'value'	=> $this->validation->search_term
            );
            echo form_input($arr);
        ?>
        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="search_term">Date range</label>
        <?php
            foreach (range(1,12) as $v) { $start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
            foreach (range(1,31) as $v) { $start_day[$v]=sprintf('%02d', $v); }
            foreach (range(date('Y')-5, date('Y')+5) as $v) { $start_yr[$v]=$v; }

            $start_mo	= array(''=>'Month') + $start_mo;
            $start_day	= array(''=>'Day') + $start_day;
            $start_yr	= array(''=>'Year') + $start_yr;

            echo form_dropdown('start_mo', $start_mo, $this->validation->start_mo);
            echo form_dropdown('start_day', $start_day, $this->validation->start_day);
            echo form_dropdown('start_yr', $start_yr, $this->validation->start_yr);
            echo form_datepicker('start_day', 'start_mo', 'start_yr', 1);
            echo ' - ';
            echo form_dropdown('end_mo', $start_mo, $this->validation->end_mo);
            echo form_dropdown('end_day', $start_day, $this->validation->end_day);
            echo form_dropdown('end_yr', $start_yr, $this->validation->end_yr);
            echo form_datepicker('end_day', 'end_mo', 'end_yr', 1);
            ?>
        <div class="clear"></div>
    </div>

    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn'), 'Search'); ?>
    </div>

    <?php echo form_close(); ?>

</div>

<?php if (!empty($results)): ?>
    <?php if (empty($results['events']) && empty($results['talks']) && empty($results['users'])): ?>
<?php $this->load->view('msg_info', array('msg' => 'Nothing found.')); ?>
    <?php else: ?>
        <?php if (!empty($results['events'])): ?>
            <div class="box">
                <h2>Events</h2>
                <?php
                foreach ($results['events'] as $k=>$v) {
                if ($v->pending==1) continue;
                    $this->load->view('event/_event-row', array('event'=>$v));
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($results['talks'])): ?>
            <div class="box">
                <h2>Talks</h2>
                <?php
                foreach ($results['talks'] as $k=>$v) {
                    $this->load->view('talk/_talk-row', array('talk'=>$v));
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($results['users'])): ?>
            <div class="box">
                <h2>Users</h2>
                <?php
                foreach ($results['users'] as $k=>$v) {
                    $this->load->view('user/_user-row', array('user'=>$v));
                }
                ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
<?php endif;

