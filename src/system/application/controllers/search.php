<?php
/**
 * Search pages controller.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * Search pages controller.
 *
 * Responsible for displaying the search page and results.
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 *
 * @property  CI_Config   $config
 * @property  CI_Loader   $load
 * @property  CI_Template $template
 * @property  CI_Input    $input
 * @property  User_model  $user_model
 */
class Search extends Controller
{

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function Main()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * Displays the search page and results when used.
     *
     * @return void
     */
    function index()
    {
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->model('talks_model');
        $this->load->model('event_model');
        $this->load->helper('reqkey');

        $results = array();
        $rules = array(
            'search_term' => 'required'
        );
        $fields = array(
            'search_term' => 'Search Term',
            'start_mo'    => 'Start Month',
            'start_day'   => 'Start Day',
            'start_yr'    => 'Start Year',
            'end_mo'      => 'End Month',
            'end_day'     => 'End Day',
            'end_yr'      => 'End Year',
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);
        $rsegments = array('search','index');

        //success! search the talks and events
        if ($this->validation->run() == true) {
            $search_term = ($this->input->post('search_term') == '/') 
                ? '' : $this->input->post('search_term');

            if (empty($search_term)) {
                $this->validation->error_string = 'Error: Empty search string!';
                $this->validation->search_term  = '';
            }

            $query = 'q:' . urlencode($search_term);
            $rsegments['q:'] = $query;

            $start    = 0;
            $end      = 0;
            $start_mo = $this->input->post('start_mo');
            $end_mo   = $this->input->post('end_mo');
            if (!empty($start_mo)) {
                $start = sprintf(
                    '%04d-%02d-%02d',
                    $this->input->post('start_yr'),
                    $this->input->post('start_mo'),
                    $this->input->post('start_day')
                );
                $query .= '/start:' . $start;
                $rsegments['start:'] = $start;
            }

            if (!empty($end_mo)) {
                $end = sprintf(
                    '%04d-%02d-%02d',
                    $this->input->post('end_yr'),
                    $this->input->post('end_mo'),
                    $this->input->post('end_day')
                );
                $query .= '/end:' . $end;
                $rsegments['end:'] = $end;
            }
        }

        $results   = null;
        array_shift($rsegments); // Remove controller
        array_shift($rsegments); // Remove action

        if (count($rsegments) > 0) {
            $rsegments = array_slice($rsegments, 0, 3);

            $search_term = null;
            $start       = null;
            $end         = null;

            foreach ($rsegments as $val) {
                if (false !== ($pos = strpos($val, 'q:'))) {
                    $search_term = substr($val, 2);
                    continue;
                }
                if (false !== ($pos = strpos($val, 'start:'))) {
                    $start = substr($val, 6);
                    continue;
                }
                if (false !== ($pos = strpos($val, 'end:'))) {
                    $end = substr($val, 4);
                    continue;
                }
            }

            if (!empty($search_term)) {
                $this->validation->search_term = urldecode($search_term);

                if (null !== $start) {
                    $start                       = max(0, @strtotime($start));
                    $this->validation->start_mo  = date('m', $start);
                    $this->validation->start_day = date('d', $start);
                    $this->validation->start_yr  = date('Y', $start);
                }

                if (null !== $end) {
                    $end                       = max(0, @strtotime($end));
                    $this->validation->end_mo  = date('m', $end);
                    $this->validation->end_day = date('d', $end);
                    $this->validation->end_yr  = date('Y', $end);
                }

                //check to see if they entered a date and set that first
                $search_term = urldecode($search_term);
                $results     = array(
                    'talks'  => $this->talks_model
                        ->search($search_term, $start, $end),
                    'events' => $this->event_model
                        ->search($search_term, $start, $end),
                    'users'  => $this->user_model
                        ->search($search_term, $start, $end)
                );
            }
        }

        $reqkey = buildReqKey();
        $arr    = array(
            'results' => $results,
            'reqkey'  => $reqkey,
            'seckey'  => buildSecFile($reqkey)
        );

        $this->template->write_view('content', 'search/main', $arr, true);
        $this->template->render();
    }
}

?>
