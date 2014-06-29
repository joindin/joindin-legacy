<?php  
/**
 * Joindin config file
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|     example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|    http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|    $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|    $route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller']  = "main";
$route['scaffolding_trigger'] = "";

//$route['event/([^add|view|edit|delete])'] = 'event/cust/$1';
$route['event/add']            = 'event/add';
$route['event/pending']        = 'event/pending';
$route['event/submit']         = 'event/submit';
$route['event/approve/(:num)'] = 'event/approve/$1';
$route['event/export/(:num)']  = 'event/export/$1';
$route['event/edit/(:num)']    = 'event/edit/$1';
$route['event/view/(:num)']    = 'event/view/$1';
$route['event/view/(:num)']    = 'event/view/$1';

$tabs = array(
    'talks',
    'comments',
    'statistics',
    'evt_related',
    'slides',
    'tracks',
    'talk_comments'
);

foreach ($tabs as $tab) {
    $route['event/view/(:num)/'.$tab] = 'event/view/$1/'.$tab;
}
$route['event/view/(:num)/track/(:num)'] = 'event/view/$1/track/$2';
$route['event/attendees/(:num)']         = 'event/attendees/$1';
$route['event/delete/(:num)']            = 'event/delete/$1';
$route['event/codes/(:num)']             = 'event/codes/$1';
$route['event/hot']                      = 'event/hot';
$route['event/all']                      = 'event/all';
$route['event/upcoming']                 = 'event/upcoming';
$route['event/past']                     = 'event/past';
$route['event/past/(:num)']              = 'event/past/$1';
$route['event/import/(:num)']            = 'event/import/$1';
$route['event/claim/(:num)']             = 'event/claim/$1';
$route['event/claim']                    = 'event/claim';
$route['event/claims']                   = 'event/claims';
$route['event/claims/(:num)']            = 'event/claims/$1';
$route['event/tracks/(:num)']            = 'event/tracks/$1';
$route['event/contact/(:num)']           = 'event/contact/$1';
$route['event/invite/([0-9]+)/?(.*)']    = 'event/invite/$1/$2';
$route['event/blog/(:any)/(:any)']       = 'event/blog/$1/$2';
$route['event/blog/feed']                = 'event/blog/feed';
$route['event/callforpapers']            = 'event/callforpapers';
$route['event/tag/(:any)']               = 'event/tag/$1';
$route['event/talk_comments/(:num)/?(:num)?'] = 'event/talk_comments/$1/$2';
//now our catch all...
$route['event/(:any)'] = 'event/cust/$1';
$route['(:num)']       = 'talk/view/$1';

$route['search/(:any)'] = 'search/index/$1';

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
