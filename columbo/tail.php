<?php

/*

Reads log file and pumps to filter logs.

*/

$logfp = "sample.log" ;

//////////////////////////////////////////////////////////////////////////

chdir( __DIR__ ) ;

if( !file_exists( $logfp ) ) {

    print( "$logfp does not exist!\n" ) ;
    exit ;

}

$size = 0 ;

while( true ) {

    clearstatcache( ) ;

    if( !file_exists( $logfp ) ) {

        usleep( 1000000 ) ;
        continue ;

    }

    $currentSize = filesize( $logfp ) ;

    if( $size == $currentSize ) {

        usleep( 1000000 ) ;
        continue;

    }

    $fh = fopen( $logfp , "r" ) ;

    fseek( $fh , $size ) ;

    $logs = array( ) ;

    while( $buffer = fgets( $fh ) ) {

        $line = trim( $buffer ) ;
        $line = str_replace( "[" , "\"" , $line ) ;
        $line = str_replace( "]" , "\"" , $line ) ;

        preg_match_all( '/"(?:\\\\.|[^\\\\"])*"|\S+/' , $line , $matches ) ;

        $logs[ ] = json_encode( $matches ) ;

    }

    fclose( $fh ) ;
    $size = $currentSize ;

    ////////////

    $totalLogs = count( $logs ) ;

    if( $totalLogs == 0 ) {

        continue ;

    }    

    $logdata = implode( "\n" , $logs ) . "\n" ;

    $filterlogs = scandir( "logs" ) ;

    foreach( $filterlogs as $v ) {

        if( substr( $v , 0 , 1 ) == "." ) {

            continue ;

        }

        file_put_contents( "logs/$v" , $logdata , FILE_APPEND ) ;

    }

}
