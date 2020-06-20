<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

interface RdbcIDataStore
{

public function getTokenId ( ) ;
public function getRoutesAssoc ( ) ;
public function updateBusRoute ( $pRoute ) ;
public function updateBusRoutes ( $pRoutesAssoc ) ;
public function dispose ( ) ;

}

?>