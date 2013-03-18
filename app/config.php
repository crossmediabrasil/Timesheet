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

// Gerando CSS
define('ALWAYS_COMPILE', false);
require_once DIR_APP . 'vendors/less_minified.inc.php';
try {
	LessMinified::ccompile(
			STATIC_DIR."css/style.less",
			STATIC_DIR."css/style.css"
	);
} catch(exception $ex) {
	exit($ex->getMessage());
}