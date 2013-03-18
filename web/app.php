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

    // Load model if exists
    System::loadModel(ucfirst($_r[1]));

    // throw new Exception('Division by zero.');
    
    var_dump(eval('$objController = new Controller' . ucfirst($_r[1]) . '();'));
    
    

    // Model exists?
    if (file_exists(DIR_APP . 'models/' . ucfirst($_r[1]) . 'Model.php')) {
        eval('$objModel = new Model' . ucfirst($_r[1]) . '();');
    }
    

    if (!empty($_r[2]) && method_exists($objController, $_r[2])) {

        eval('$objController->' . $_r[2] . '();');

    } else {

        eval('$objController->indexAction();');

    }

} else {

    // Load model if exists
    System::loadModel(DEFAULT_CONTROLLER);
    
    $_file = DIR_CONTROLLERS . DEFAULT_CONTROLLER.'Controller.php';
    if (file_exists($_file)) {
    	include_once($_file);
    }
    
    eval('$objController = new Controller' . DEFAULT_CONTROLLER . '();');
    eval('$objController->indexAction();');
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