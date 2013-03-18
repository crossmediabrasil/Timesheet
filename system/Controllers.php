<?php
/**
 * Controlador de ações
 * 
 * PHP version 5
 * 
 * @category System
 * @package  Kaffeine
 * @author   Michel Wilhelm <michelwilhelm@gmail.com>
 * @license  GPL http://michelw.in
 * @version  GIT: $Id$
 * @link     http://michelw.in/
 */

/**
 * Create a controller interface for all system
 * 
 * @name     Controllers
 * @category System
 * @package  Kaffeine
 * @author   Michel Wilhelm <michelwilhelm@gmail.com>
 * @license  GPL http://michelw.in
 * @link     http://michelw.in/
 */
class Controllers extends System
{

    /**
     * constructor
     * 
     * @return Bool
     */
    function __construct()
    {
        
    }

    public static function load ($_controller=false) 
    {
        if ($_controller !== false) {
            // Model exists?
            if (file_exists(DIR_APP . 'controllers/' . $_controller . 'Controller.php')) {
                include_once DIR_APP . 'controllers/' . $_controller . 'Controller.php';
                eval('$objModel = new Controller' . $_controller . '();');
                return $objModel;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}