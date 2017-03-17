<?php

/*

Opens a log file and sends lines to output at some flow rate.
Useful for creating traffic from a static log file.
Or to have another layer.

*/

// Change this to log file path
$logfp = "/Users/zafar/tmp/access_log" ;

// If filepath does not exist then exit
if( !file_exists( $logfp ) ) {

    print( "$logfp does not exist!\n" ) ;
    exit ;

}

// Size holds the last filesize of log file 
$size = 0 ;

// Run loop
while( true ) {

    // For file size requests to always return fresh values we must clear the FS cache
    clearstatcache( ) ;

    // Just in case log file has dissappeared (e.g. log rotation)
    if( !file_exists( $logfp ) ) {

        usleep( 1000000 ) ;
        continue ;

    }

    // Do nothing if log is the same file size
    $currentSize = filesize( $logfp ) ;

    if( $size == $currentSize ) {

        usleep( 1000000 ) ; 
        continue;

    }

    // Open log file and seek to last position
    $fh = fopen( $logfp , "r" ) ;

    fseek( $fh , $size ) ;

    // Send the lines to output
    while( $buffer = fgets( $fh ) ) {

        $line = trim( $buffer ) ;

        print( "$line\n" ) ;

        usleep( 100000 ) ;

    }

    fclose( $fh ) ;

    // Update current file size
    $size = $currentSize ;

}
