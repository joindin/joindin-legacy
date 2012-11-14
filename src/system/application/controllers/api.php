<?php
/**
 * API pages controller.
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
 * API pages controller.
 *
 * Responsible for handling calls to the API and returning data in the given
 * output format (i.e. JSON).
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
 * @property  User_model  $user_model
 */
class Api extends Controller
{

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function Api()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * Displays the API documentation as a web page.
     *
     * @return void
     */
    function index()
    {
        //show our docs
        $this->template->write_view('content', 'api/doc');
        $this->template->render();
    }

    /**
     * Redirects the API calls to the 'event' service handler.
     *
     * @return void
     */
    function event()
    {
        $this->load->library('service');
        $data = file_get_contents('php://input');
        $ret  = array(
            'out' => $this->service->handle('event', $data)
        );
        $this->output($ret);
    }

    /**
     * Redirects the API calls to the 'talk' service handler.
     *
     * @return void
     */
    function talk()
    {
        $this->load->library('service');
        $data = file_get_contents('php://input');
        $ret  = array(
            'out' => $this->service->handle('talk', $data)
        );
        $this->output($ret);
    }

    /**
     * Redirects the API calls to the 'comment' service handler.
     *
     * @return void
     */
    function comment()
    {
        $this->load->library('service');
        $data = file_get_contents('php://input');
        $ret  = array(
            'out' => $this->service->handle('comment', $data)
        );
        $this->output($ret);
    }

    /**
     * Redirects the API calls to the 'blog' service handler.
     *
     * @return void
     */
    function blog()
    {
        $this->load->library('service');
        $data = file_get_contents('php://input');
        $ret  = array(
            'out' => $this->service->handle('blog', $data)
        );
        $this->output($ret);
    }

    /**
     * Redirects the API calls to the 'user' service handler.
     *
     * @return void
     */
    function user()
    {
        $this->load->library('service');
        $data = file_get_contents('php://input');
        $ret  = array(
            'out' => $this->service->handle('user', $data)
        );
        $this->output($ret);
    }

    /**
     * Redirects the API calls to the 'site' service handler.
     *
     * @return void
     */
    function site()
    {
        $this->load->library('service');
        $data = file_get_contents('php://input');
        $ret  = array(
            'out' => $this->service->handle('site', $data)
        );
        $this->output($ret);
    }

    /**
     * Loads the correct output type's template and parses the provided
     * data array for display.
     *
     * @param array $ret The values returned from the service handler
     * 
     * @return void
     */
    function output($ret)
    {
        // ret contains element out with elements output (format) and data
        $out = null;
        if (isset($ret['out'])) {
            if (isset($ret['out']['output']) && is_string($ret['out']['output'])) {
                $out = 'out_' . $ret['out']['output'];
            } else {
                $out = 'out_json';
            }
            $params = $ret['out'];
            if (isset($ret['out']['data'])) {
                $params = $ret['out']['data'];
            }
            $this->load->view('api/' . $out, $params);
        } else {
            $arr = array('items' => array(
                'msg' => 'Unknown Error'
            ));
            $this->load->view('api/out_json', $arr);
        }
    }

    /**
     * Returns timezone information to the invoker as JSON.
     *
     * @return void
     */
    function tz()
    {
        $this->load->model('tz_model');

        $out = $this->tz_model->getOffsetInfo();
        echo json_encode($out);
    }

    /**
     * Documentation for the v2 API
     * 
     * @return void
     */
    public function v2docs() 
    {
        $this->template->write_view('content', 'api/v2docs');
        $this->template->render();
    }

    /**
     * Documentation for the v1 API
     * 
     * @return void
     */
    public function v1docs() 
    {
        $this->template->write_view('content', 'api/v1docs');
        $this->template->render();
    }
}

