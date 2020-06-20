<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

header( 'Content-Type: application/json' ) ;
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ) ;
header( 'Cache-Control: no-store, no-cache, must-revalidate' ) ;
header( 'Cache-Control: post-check=0, pre-check=0' , false ) ;
header( 'Pragma: no-cache' ) ; // HTTP/1.0
header( "Expires: " . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ) ;

$view = new RdbcJsonView() ;
$request = $view->getRequest() ;

$dataStoreManager = new RdbcMysqlDataStoreManager() ;
$dataStoreManager->open() ;
$controller = new RdbcController( $dataStoreManager ) ;

$controller->setRequest( $request ) ;
$controller->processRequest() ;

$result = $controller->getResult() ;
$view->processResult( $result ) ;

$dataStoreManager->close() ;

echo $view->toJsonResponseString() ;

?>