<?php
/**
 * Processa todas as requisições solicitadas
 * 
 * PHP version 5
 * 
 * @category Base
 * @package  Kaffeine
 * @author   Michel Wilhelm <michelwilhelm@gmail.com>
 * @license  GPL http://michelw.in
 * @version  GIT: $Id$
 * @link     http://michelw.in/
 */
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/route.php';
require_once DIR_APP . 'vendors/Layout.php';

$_r = System::rewrite();

$Models = new Models();

System::seoTitle(APP_TITLE);
if (count($_r) > 1) {

	  // Model exists?
    if (file_exists(DIR_MODELS . ucfirst($_r[1]) . '.php')) {
    	
    		System::loadModel(ucfirst($_r[1]));
    	
        eval('$objModel = new Model' . ucfirst($_r[1]) . '();');
    }
    
    // Controller exists?
    if (file_exists(DIR_CONTROLLERS . ucfirst($_r[1]) . '.php')) {
	    	$objController = System::loadController(ucfirst($_r[1]));
	    	
	    	if (!empty($_r[2]) && method_exists($objController, $_r[2])) {
	    			eval('$objController->' . $_r[2] . '();');
	    	} else {
	    			eval('$objController->indexAction();');
	    	}
    }
   

} else {
    
    // Model exists?
    if (file_exists(DIR_MODELS . DEFAULT_CONTROLLER . '.php')) {
	    	 
	    	System::loadModel(DEFAULT_CONTROLLER);
	    	 
	    	eval('$objModel = new Model' . DEFAULT_CONTROLLER . '();');
    }
    
    // Controller exists?
    if (file_exists(DIR_CONTROLLERS . DEFAULT_CONTROLLER . '.php')) {
	    	$objController = System::loadController(DEFAULT_CONTROLLER);
	   		eval('$objController->indexAction();');
    }
}

// Comprimindo arquivo de log do dia anterior
$_file = DEFAULT_LOG_DIR.date('Y_m_d', strtotime('-1 days')).'_access.log';
if (file_exists($_file)) {
    shell_exec("gzip {$_file}");
}
$_file = DEFAULT_LOG_DIR.date('Y_m_d', strtotime('-1 days')).'_error.log';
if (file_exists($_file)) {
    shell_exec("gzip {$_file}");
}