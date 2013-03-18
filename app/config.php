<?php
define('__ROOT__', __DIR__ . '/../');

define('DIR_APP', str_replace('//', '/', __DIR__ . '/'));
define('DIR_CACHE', str_replace('//', '/', __DIR__ . '/cache/'));
define('REWRITE_EXT', '');

switch ($_SERVER['SERVER_NAME']) {
default:
    if (file_exists(DIR_APP . 'config/'.$_SERVER['SERVER_NAME'].'.php')) {
        include_once DIR_APP . 'config/'.$_SERVER['SERVER_NAME'].'.php';
    } else {
        require_once DIR_APP . 'config/default.php';    
    }
}

define('DEFAULT_CONTROLLER', 'Home');

require_once __ROOT__ . 'global_config.php';
