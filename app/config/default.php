<?php
define('__PATH__', '/web/');

// Database connection
define('DB_HOST', 'mongo_host');
define('DB_USER', 'mongo_user');
define('DB_PASSWORD', 'mongo_pass');
define('DB_PORT', 27017);
define('DB_DB', 'mongo_db');

define('GridFS_HOST_0', 'localhost');
define('GridFS_USER_0', '');
define('GridFS_PASSWORD_0', '');
define('GridFS_PORT_0', 27017);
define('GridFS_DB_0', '');

define('GridFS_HOSTS', 0);


define('__PASS_SALT__', md5('AnSaltHere'));

define('STATIC_URL', 'http://yourcdn.specific.url.com/');

define('EMAIL_FROM_NAME', 'Email From Name');
define('EMAIL_FROM_EMAIL', 'user@host.com');

// Assunto padrão do email de confirmação
define('EMAIL_CONFIRMATION_SUBJECT', 'E-mail confirmation subject');

define('APP_TITLE', 'Timesheet');
define('STATIC_DIR', __ROOT__ . 'web/');
define('__TITLE_SEP__', '-');
define('__TITLE_SEP_INV__', '-');