<?php
/**
 * Configurações globais do sistema
 * 
 * PHP version 5
 * 
 * @category Base
 * @package  Timesheet
 * @author   Michel Wilhelm <michelwilhelm@gmail.com>
 * @license  GPL http://michelw.in
 * @version  GIT: $Id$
 * @link     http://michelw.in/
 */

require_once __ROOT__ . 'config.php';

if (!defined('DEFAULT_TZ')) {
    define('DEFAULT_TZ', 'UTC');
}

if (!defined('DIR_APP')) {
    define('DIR_APP', __ROOT__ . 'app/');
}

if (!defined('DIR_CONTROLLERS')) {
	define('DIR_CONTROLLERS', DIR_APP.'controllers/');
}

if (!defined('DIR_MODELS')) {
	define('DIR_MODELS', DIR_APP.'models/');
}

if (!defined('DIR_VENDOR')) {
    define('DIR_VENDOR', __ROOT__ . 'vendor/');
}

// Simplesmente um alias
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = gethostname();
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['SERVER_PORT'] = 80;
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}

if (!defined('__DNS__')) {
    define('__DNS__', $_SERVER['SERVER_NAME']);        
}
if (!defined('COOKIE_LIFETIME')) {
    define('COOKIE_LIFETIME', (7 * 24 * 60 * 60));
}
if (!defined('__SESSION_TIMEOUT__')) {
    define('__SESSION_TIMEOUT__', 3600);
}
if (!defined('__SESSION_NAME__')) {
    define('__SESSION_NAME__', 'ANIW');
}
if (!defined('DEFAULT_LOCALE')) {
    define('DEFAULT_LOCALE', 'pt_BR');
}
if (!defined('DEFAULT_TIMEZONE')) {
    define('DEFAULT_TIMEZONE', 'America/Sao_Paulo');
}
if (!defined('__LOCALE__')) {
    // Define o idioma padrão
    define('__LOCALE__', DEFAULT_LOCALE);  
}

//*** Cabeçalhos
header('Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"', true);
header('Content-Type: text/html; charset=UTF-8');
header('Expires: ' . date('D, d m Y H:i:s') . ' GMT');
header('X-Server-Name: ' . __DNS__);



date_default_timezone_set(DEFAULT_TIMEZONE);

//*** Renomeando sessão
ini_set('session.name', __SESSION_NAME__);
define('SESSION_NAME', __SESSION_NAME__);

session_start();

// Setando tempo de vida da sessão
if (isset($_COOKIE['sid'])) {
    setcookie(__SESSION_NAME__, $_COOKIE['sid'], time() + (72 * 60 * 60), __PATH__);
    setcookie('sid', $_COOKIE['sid'], time() + (72 * 60 * 60), __PATH__);  
} else {
    setcookie(__SESSION_NAME__, session_id(), time() + (72 * 60 * 60), __PATH__);
    setcookie('sid', session_id(), time() + (72 * 60 * 60), __PATH__);
}
if (count($_SESSION) === 0) {
    $_SESSION = Array(
        'conn' => null,
        'SEO' => Array(
            'title' => null,
            'tags' => null,
            'description' => null
        )
    );
} else {
    $_SESSION['conn'] = null;
    $_SESSION['SEO']['title'] = null;
    $_SESSION['SEO']['tags'] = null;
    $_SESSION['SEO']['description'] = null;
}

//*** Configurando o sistema
mb_internal_encoding("UTF-8");

ini_set("display_errors", true);
setlocale(LC_ALL, __LOCALE__ . ".UTF-8");

define('DEFAULT_LOG_DIR', __ROOT__.'logs/');
shell_exec('mkdir -p '.DEFAULT_LOG_DIR);

define('DEFAULT_LOG_FILE_ACCESS', DEFAULT_LOG_DIR.date('Y_m_d').'_access.log');
define('DEFAULT_LOG_FILE_ERROR', DEFAULT_LOG_DIR.date('Y_m_d').'_error.log');
