<?php

/*

Handler for the client request.

*/

date_default_timezone_set( "EET" ) ;

chdir( __DIR__ ) ;

$debug = "" ;

$filters = explode( "," , $_GET[ "filters" ] ) ;
$token = $_GET[ "token" ] ;

$timeNow = time( ) ;

$data = array( ) ;

///////////////

foreach( $filters as $filtername ) {

    $filter = "data/" . trim( $filtername ) . ".json" ;

    if( file_exists( $filter ) ) {

        $filterdata = json_decode( file_get_contents( $filter ) , true ) ;

        $data = array_merge( $data , $filterdata ) ;

    }

}

$maxTime = -1 ;

foreach( $data as $k=>$v ) {

    if( $maxTime < $v[ "_time_" ] ) {

        $maxTime = $v[ "_time_" ] ;

    }

}

foreach( $data as $k=>$v ) {

    if( ( $maxTime - $v[ "_time_" ] ) > 600 ) {

        unset($data[$k]);

    }

}

$data = array_values( $data ) ;

$state="nodata";
if(count($data)>0){
    $state = "newdata" ;
}

///////////////

$res = array( ) ;

$res[ "debug" ] = $debug ;
$res[ "state" ] = $state ;
$res[ "items" ] = $data ;

///////////////

header( "Content-Type: application/json;charset=utf-8" ) ;
print( json_encode( $res) ) ;
