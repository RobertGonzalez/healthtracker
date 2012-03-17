<?php
class database{
    private static $dbh,$dns,$user,$password;
    
    private function __construct(){
    }
    
    public static function get_instance(){
       if(!isset(self::$dbh)){
            self::$dns = 'mysql:dbname=charlesd_weight;host=localhost';
            self::$user = 'charlesd_weight';
            self::$password = 'feb31986';
            self::$dbh = new PDO(self::$dns, self::$user, self::$password);
       }
       return self::$dbh;
    }
}