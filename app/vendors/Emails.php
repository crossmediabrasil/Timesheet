<?php
class Emails extends System {

    private static $__to_name    = false;
    private static $__to_email   = false;

    private static $__from_name  = false;
    private static $__from_email = false;

    private static $__subject    = false;
    private static $__body       = false;

    private static $__error      = false;

    function __construct()
    {

    }

    public static function setTo($_name = false, $_email = false)
    {
        if ($_name !== false && $_email !== false) {
            self::$__to_name = $_name;
            self::$__to_email = $_email;
        } else {
            self::$__error = true;
        }
    }

    public static function setFrom ($_name = false, $_email = false)
    {
        if ($_name !== false && $_email !== false) {
            self::$__from_name = $_name;
            self::$__from_email = $_email;
        } else {
            self::$__error = true;
        }
    }

    public static function setSubject ($_subject = false)
    {
        if ($_subject !== false) {
            self::$__subject = $_subject;
        } else {
            self::$__error = true;
        }
    }

    public static function setBody ($_body = false)
    {
        if ($_body !== false) {
            self::$__body = $_body;
        } else {
            self::$__error = true;
        }
    }

    public static function send ()
    {
        if (self::$__error === false) {

            include_once DIR_VENDOR . 'Swift/lib/swift_required.php';

            // Criando transporte
            $transport = Swift_MailTransport::newInstance();

            $mailer = Swift_Mailer::newInstance($transport);

            // Create a message
            $message = Swift_Message::newInstance(self::$__subject)
              ->setFrom(array(self::$__from_email => self::$__from_name))
              ->setTo(array(self::$__to_email => self::$__to_name))
              ->setBody(self::$__body)
              ;

            // Send the message
            $result = $mailer->send($message);

            return true;

        } else {
            return false;
        }
    }
}