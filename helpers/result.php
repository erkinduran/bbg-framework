<?php
    
    if ( !function_exists ( "r" ) )
    {
        function r ( $query, $message = NULL, $failedmessage = NULL, $dmessage = NULL )
        {
            $result = [
                "status"  => $query ? TRUE : FALSE,
                "message" => $query ? $message : $failedmessage,
            ];
            if ( $dmessage ) $result[ "dmessage" ] = $dmessage;
            header ( 'Content-Type: application/json' );
            
            return convert_from_latin1_to_utf8_recursively ( $result );
        }
    }