<?php
/**
 * Configurações gerais do sistema
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

require_once __ROOT__ . 'system/System.php';
require_once __ROOT__ . 'system/Models.php';
require_once __ROOT__ . 'system/Controllers.php';
require_once __ROOT__ . 'system/Views.php';

// Composer
require_once __ROOT__ . 'vendor/autoload.php';

/**
 * Auto load classes
 *
 * @param String $reqClass Class name to load
 *
 * @return Boolean
 */
function __autoload ($reqClass)
{
    if (!preg_match("(Model|View|Controller)", $reqClass, $_fragment)) {

    }

    $patterns = Array('/' . $_fragment[0] . '/');
    $replacements = array();
    $replacements[0] = '';

    $_class_name = preg_replace($patterns, $replacements, $reqClass);
    $_type = $_fragment[0];
    $_file = DIR_APP . strtolower($_type) . 's/';
    $_file .= $_class_name . '.php';

    if (file_exists($_file)) {

        include_once $_file;

    } else {
        switch ($_type) {
        case 'controller';
            print 'Controller <strong>' . $_class_name . '</strong> not found';
            break;

        case 'view':
            print 'View <strong>' . $_class_name . '</strong> not found';
            break;

        case 'model':
            print 'Model <strong>' . $_class_name . '</strong> not found';
            break;
        }
    }

    return true;
}

