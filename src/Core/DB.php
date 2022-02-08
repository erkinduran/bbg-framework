<?php
    
    namespace App\Core;
    
    use PDO;
    use PDOException;
    
    class DB
    {
        protected static $dbh = NULL;
        
        public function __construct ()
        {
            if ( self::$dbh == NULL )
            {
                try
                {
                    self::$dbh = new PDO( 'mysql:host=' . env ( 'DB_HOST' ) . ':' . env ( 'DB_PORT' ) . ';dbname=' . env ( 'DB_DATABASE' ), env ( 'DB_USERNAME' ), env ( 'DB_PASSWORD' ), [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ] );
                }
                catch ( PDOException $ex )
                {
                }
            }
        }
        
        public static function check ()
        {
            if ( self::$dbh == NULL )
            {
                die( json_encode ( [
                    'outcome' => FALSE,
                    'message' => 'Unable to connect'
                ] ) );
            }
        }
        
        public static function prepare ( $str )
        {
            self::check ();
            
            return self::$dbh->prepare ( $str );
        }
        
        public static function insert ( $str )
        {
            self::check ();
            
            return self::$dbh->query ( $str );
        }
        
        public static function select ( $str )
        {
            self::check ();
            
            return self::$dbh->query ( $str );
        }
        
        public static function close ()
        {
            self::$dbh = NULL;
        }
    }