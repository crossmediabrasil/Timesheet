<?php
class ModelUsers extends Models
{
    private static $__indexes = Array(
      Array( Array( 'username' => 1 ) ),
      Array( Array( 'email'    => 1 ) ),
    );

    function __construct() {
      self::$__collectionName = 'Users';
    }

    public static function newAccount()
    {
        if (count($_POST) > 0) {
            ModelUsers::connect();
            $_data = $_POST['form'];
            $_data['password'] = self::passwd(trim($_data['password']));
            $_data['stats'] = Array(
              'last_login'      => false,
              'last_activity'   => new MongoDate(time()),
              'updated'         => new MongoDate(time()),
              'created'         => new MongoDate(time()),
              'wallpapers'      => 0,
              'like_received'   => 0,
              'like_gived'      => 0,
              'unlike_received' => 0,
              'unlink_sent'     => 0,
              'karma'           => 0.001,
              'quota_max'       => 0,
              'quota_used'      => 0,
              'logins'          => 0
            );

            ModelUsers::save($_data);
            self::redirect(self::location('users/login', true));
        }
    }


    public static function login()
    {
        ModelUsers::connect();
        $_data = $_POST['form'];
        $_data['password'] = self::passwd($_data['password']);

        $_find = Array(
            '$or' => Array(
                Array(
                    'email' => $_data['username'],
                    'password' => $_data['password']
                ),
                Array(
                    'username' => $_data['username'],
                    'password' => $_data['password']
                ),
            )
        );
        $_user = ModelUsers::findOne($_find);

        if (is_array($_user)) {
            $_SESSION['online']       = true;
            $_SESSION['online_since'] = time();
            $_SESSION['uid']          = $_user['_id']->__toString();

            $_user['stats']['last_login']    = new MongoDate(time());
            $_user['stats']['last_activity'] = new MongoDate(time());
            $_user['stats']['logins']++;
            $_user['stats']['karma'] = $_user['stats']['karma']+0.001;
            ModelUsers::save($_user);

            self::redirect(self::location('wallpapers/my', true));
        } else {
            self::redirect(self::location('users/login', true));
        }
    }

    public static function logout()
    {
        unset($_SESSION);
        session_destroy();
        self::redirect(self::location(false, true));        
    }

}