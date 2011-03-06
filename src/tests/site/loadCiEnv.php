<?php
$paths = array(
        '../../system/libraries',
        '../../system/codeigniter',
        '../../system/database'
);

set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR,$paths));

function loader($class){

        switch(strtolower($class)){
                case 'ci_db': $class = 'DB'; break;
                case 'ci_loader': $class = 'Loader'; break;
        }

        include_once($class.'.php');
}
spl_autoload_register('loader');


define('BASEPATH','../../system/');
define('EXT','.php');
define('APPPATH','../../system/application/');

require(BASEPATH.'codeigniter/Common'.EXT);
require(BASEPATH.'codeigniter/Compat'.EXT);
require(APPPATH.'config/constants'.EXT);

$CFG =& load_class('Config');
$URI =& load_class('URI');
$RTR =& load_class('Router');
$OUT =& load_class('Output');

require(BASEPATH.'codeigniter/Base5'.EXT);
load_class('Controller');
?>
