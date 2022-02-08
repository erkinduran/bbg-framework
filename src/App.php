<?php
    
    namespace App;
    
    class App
    {
        public function run ()
        {
            try
            {
                $routes = require __DIR__ . "/../config/route.php";
                $class  = new $routes[ $_SERVER[ 'REQUEST_URI' ] ][ 0 ]();
                
                return $class->{$routes[ $_SERVER[ 'REQUEST_URI' ] ][ 1 ]}();
            }
            catch ( \Exception $exception )
            {
                echo $exception->getMessage ();
                exit;
            }
        }
    }