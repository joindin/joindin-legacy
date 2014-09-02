<?php
$cl=array();

// work through the talks list and split into days
$by_day=array();
foreach ($talks as $t) {
    $day = strtotime($t->display_date);
    $by_day[$day][]=$t;
}
ksort($by_day);
$ct=0;
$tabs = new joindIn_TabContainer();
$tabs
    ->setBaseUrl($event_url)
    ->setContainerName('event');

$talksTab = new joindIn_Tab('talks', 'Talks ('. count($talks). ')');
$talksTab
    ->setId('talks')
    ->setContent(
        $this->load->view(
            'event/modules/_event_tab_talks',
            array(
                'by_day'	=> $by_day,
                'cl'		=> $cl,
                'ct'		=> $ct,
                'claims'	=> $claimed
            ),
            true
        )
    );

$commentsTab = new joindIn_Tab(
    'comments',
    'Comments ('.count($comments).')',
    $this->load->view('event/modules/_event_tab_comments', array(), true)
);

$tabs->addTab($talksTab);
$tabs->addTab($commentsTab);

if (isset($evt_sessions) && count($evt_sessions)>0):
    $relatedTab = new joindIn_Tab(
        'evt_related',
        'Event related ('.count($evt_sessions).')',
        $this->load->view('event/modules/_event_tab_evtrelated', array(), true)
    );
    $tabs->addTab($relatedTab);
endif;

$slidesTab = new joindIn_Tab(
    'slides',
    'Slides ('.count($slides_list).')',
    $this->load->view('event/modules/_event_tab_slides', array('ct'=>$ct), true)
);
$tabs->addTab($slidesTab);

if ($admin):
    $statsTab = new joindIn_Tab(
        'statistics',
        'Statistics',
        $this->load->view('event/modules/_event_tab_admin', array('talk_stats'=>$talk_stats), true)
    );
    $tabs->addTab($statsTab);
endif;

if (count($tracks)>0):
    $tracksTab = new joindIn_Tab(
        'tracks',
        'Tracks ('.count($tracks).')',
        $this->load->view('event/modules/_event_tab_tracks', array(), true)
    );
$tabs->addTab($tracksTab);
endif;

$talk_comment_count = array_reduce(
    $talks,
    function($sum, $talk) { return $sum + $talk->comment_count; }
);
$talk_comment_count += array_reduce(
    $evt_sessions,
    function($sum, $session) { return $sum + $session->comment_count; }
);
if (count($talk_comment_count) > 0):
    $talkCommentsTab = new joindIn_Tab(
        'talk_comments',
        'Recent comments ('.$talk_comment_count.')',
        $this->load->view('event/modules/_event_tab_talk_comments', array(), true)
    );
    $tabs->addTab($talkCommentsTab);
endif;
//$tabs->addTab('hello', 'hi', 'Tab Content', '#hi');
//$tabs->addTab('hello', 'hi', 'Tab Content 2', '#hi2s');

echo $tabs->setSelectedTab($tab);
