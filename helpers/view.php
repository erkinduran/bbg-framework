<?php
    
    if ( !function_exists ( "view" ) )
    {
        /**
         * @throws Exception
         */
        function view ( $filename, array $data = [] )
        {
            $file = MAIN_PATH . '/views/' . strtolower ( $filename ) . '.php';
            
            if ( file_exists ( $file ) )
            {
                ob_start ();
                extract($data);
                include $file;
                $contents = ob_get_contents ();
                ob_end_clean ();
                
                return $contents;
            }
            else
            {
                throw new Exception( 'Template ' . $filename . ' not found!' );
            }
        }
    }