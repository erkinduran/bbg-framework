<?php
    
    namespace App\Front;
    
    use App\Bags\Calculate;
    use App\Core\DB;
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