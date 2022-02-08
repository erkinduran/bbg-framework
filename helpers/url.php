<?php
    if ( !function_exists ( 'url' ) )
    {
        function url ( $path = "/" )
        {
            $path = ltrim ( $path, "/" );
            $url  = env ( 'APP_URL' );
            $url = rtrim ( $url, "/" );
            if ( filter_var ( env ( 'SSL_SECURE' ), FILTER_VALIDATE_BOOLEAN ) == TRUE )
            {
                if ( strpos ( $url, "https" ) === FALSE )
                {
                    $url = "https://" . $url;
                }
            }
            else
            {
                if ( strpos ( $url, "http" ) === FALSE )
                {
                    $url = "http://" . $url;
                }
            }
            
            return $url . "/" . $path;
        }
    }