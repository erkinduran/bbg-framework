<?php
    /*
     * Development router for PHP's built-in server:
     *
     *     php -S 127.0.0.1:8000 server.php
     *
     * Existing files (public/css/app.css, public/js/jquery.js, ...) are served
     * straight off disk; everything else is handed to the front controller.
     * Not meant for production -- use Apache or nginx there.
     */
    
    $path = parse_url ( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
    
    if ( $path !== '/' && file_exists ( __DIR__ . $path ) )
    {
        return FALSE;
    }
    
    require __DIR__ . '/index.php';
