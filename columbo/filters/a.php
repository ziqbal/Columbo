<?php

/*

Filter for requests per second

*/

date_default_timezone_set( "EET" ) ;

chdir( __DIR__ . "/../" ) ;

$filter = pathinfo( __FILE__ , PATHINFO_FILENAME ) ;

$logfp = "logs/$filter.log" ;

system( "touch $logfp.tmp" ) ;
system( "mv $logfp.tmp $logfp" ) ;

$data = array( ) ;

$logsize = 0 ;

$hits = 0 ;
$lastTime = -1 ;

while( true ) {

    clearstatcache( ) ;

    $currentSize = filesize( $logfp ) ;

    if( $logsize == $currentSize ) {

        usleep( 1000000 ) ;
        continue;

    }

    $fh = fopen( $logfp , "r" ) ;

    fseek( $fh , $logsize ) ;

    $logs = array( ) ;

    while( $buffer = fgets( $fh ) ) {

        $logs[ ] = json_decode( trim( $buffer ) , true ) ;

    }

    fclose( $fh ) ;

    $logsize = $currentSize ;

    foreach( $logs as $log ) {

        $t = strtotime( substr( $log[ 0 ][ 4 ] , 1 , -1 ) ) ;

        $hits ++;

        if( $t > $lastTime ) {

            $z = array( ) ;

            $z[ "_time_" ] = $t ;

            $z[ "period" ] = date( "Y-m-d H:i:s" , $t ) ;

            $z[ $filter ] = $hits ;

            $data[ ] = $z ;

            $hits = 0 ;
            $lastTime = $t ;

            if( count( $data ) > 100 ) {

                array_shift( $data ) ;

            }

        }

    }

    file_put_contents( "data/$filter.json.tmp" , json_encode( $data ) ) ;
    system( "mv data/$filter.json.tmp data/$filter.json" ) ;

    sleep( 1 ) ;

}