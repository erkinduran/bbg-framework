<?php
    if ( !function_exists ( "convert_from_latin1_to_utf8_recursively" ) )
    {
        function convert_from_latin1_to_utf8_recursively ( $dat )
        {
            if ( is_string ( $dat ) )
            {
                return mb_convert_encoding ( $dat, 'UTF-8', 'UTF-8' );;
            }
            else if ( is_array ( $dat ) )
            {
                $ret = [];
                foreach ( $dat as $i => $d ) $ret[ $i ] = convert_from_latin1_to_utf8_recursively ( $d );
                
                return $ret;
            }
            else if ( is_object ( $dat ) )
            {
                foreach ( $dat as $i => $d ) $dat->$i = convert_from_latin1_to_utf8_recursively ( $d );
                
                return $dat;
            }
            else
            {
                return $dat;
            }
        }
    }