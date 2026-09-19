<?php
    
    namespace App\Front;
    
    use Exception;

    class Home
    {
        /**
         * @throws Exception
         */
        public function index ()
        {
            return view ( "welcome" );
        }
    }
