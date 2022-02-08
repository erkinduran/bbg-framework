<?php
    
    use App\App;
    use App\Core\DB;
    use Dotenv\Dotenv;
    
    if ( !file_exists ( 'vendor/autoload.php' ) )
    {
        exit ( "Composer not installed" );
    }
    
    require 'vendor/autoload.php';
    
    $dotenv = Dotenv::createImmutable ( __DIR__ );
    $dotenv->load ();
    
    const MAIN_PATH = __DIR__;
    const APP_PATH  = __DIR__ . "/src";
    
    $path = 'helpers/*.php';
    
    $filenames = glob ( $path );
    
    foreach ( $filenames as $filename )
    {
        require $filename;
    }
    
    $db = new DB();
    
    $app    = new App();
    $output = $app->run ();
    
    $db::close ();
    
    if ( is_array ( $output ) )
    {
        header ( "Content-type: application/json; charset=utf-8" );
        
        echo json_encode ( $output );
    }
    else if ( is_object ( $output ) )
    {
        header ( "Content-type: application/json; charset=utf-8" );
        
        echo json_encode ( $output );
    }
    else
    {
        echo $output;
    }