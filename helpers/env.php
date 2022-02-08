<?php
    if ( !function_exists ( 'env' ) )
    {
        function env ( $ENV )
        {
            return $_ENV[ $ENV ] ?? NULL;
        }
    }