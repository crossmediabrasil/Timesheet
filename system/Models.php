<?php
/**
 * Models
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
 * Create a data model
 * 
 * @name     Models
 * @category System
 * @package  Kaffeine
 * @author   Michel Wilhelm <michelwilhelm@gmail.com>
 * @license  GPL http://michelw.in
 * @link     http://michelw.in/
 */
class Models extends System
{

    public static $__db         = 'Kaffeine';
    public static $__user       = false;
    public static $__host       = 'localhost';
    public static $__port       = 27017;
    public static $__pass       = false;
    public static $__conn       = false;
    public static $__collection = false;
    public static $__data       = Array();

    // Nome da coleção
    public static  $__collectionName = null;

    /**
     * constructor
     * 
     * @return Bool
     */
    function __construct ($_args=Array())
    {

        self::$__user = DB_USER;
        self::$__pass = DB_PASSWORD;
        self::$__host = DB_HOST;
        self::$__port = DB_PORT;
        self::$__db   = DB_DB;

        if (is_object($_SESSION['conn']) === false && self::$__conn === false) {
            try {
                self::$__conn = new Mongo(
                    sprintf("mongodb://%s:%s@%s:%s/%s",
                        self::$__user,
                        self::$__pass,
                        self::$__host,
                        self::$__port,
                        self::$__db
                   )
               );
                
                $_db = self::$__db;
                self::$__conn = self::$__conn->$_db;
                $_SESSION['conn'] = self::$__conn;

            } catch (MongoConnectionException $e) {
                printf(
                    "Erro ao se conectar-se em %s/%s:%s",
                    self::$__host,
                    self::$__db,
                    self::$__port
               );
            }
        }
    }

    public static function connect ($obj=false)
    {
        self::$__collection     = str_replace('Model', '', get_called_class());
        $_collection            = self::$__collection;
        self::$__conn           = $_SESSION['conn'];
        self::$__conn           = self::$__conn->$_collection;
    }

    public static function Collection()
    {
        self::connect();
        return self::$__conn;
    }  
      
    public static function save($_args = false) 
    {
        if(is_array($_args)) {
            self::Collection()->save($_args);
        }
    }

    public static function findOne($_args = false) 
    {
        if(is_array($_args)) {
            return self::Collection()->findOne($_args);
        } else {
            return self::Collection()->findOne();
        }
    }
    
    public static function getCollectionName() 
    {
        return self::$__collection;
    }
      
    public static function indexRebuild()
    {
      
        // Removing old indexes
        self::Collection()->deleteIndexes();

        // Reindexing with new parameters
        foreach(self::$__indexes as $_index) {
            if(count($_index) == 1) {
                self::Collection()->ensureIndex($_index[0]);
            } else {
                self::Collection()->ensureIndex($_index[0], $_index[1]);
            }

        }

        return true;
    }

    public static function load ($_model=false) 
    {
        if ($_model !== false) {
            // Model exists?
            if (file_exists(DIR_APP . 'models/' . $_model . 'Model.php')) {
                include_once DIR_APP . 'models/' . $_model . 'Model.php';
                eval('$objModel = new Model' . $_model . '();');
                return $objModel;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}