<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

final class RdbcResultType
{
  const
    None = 0 ,
    Error = 1 ,
    Initialized = 2 ,
    RouteSelected = 3 ,
    TicketsSelected = 4 ,
    PurchaseComplete = 5 ;
}

interface RdbcIResult
{

public function getResultType ( ) ;
public function getSessionId ( ) ;
public function getRoutesAssoc ( ) ;
public function getRouteIdSelected ( ) ;
public function getTicketsSelected ( ) ;
public function getPurchaseConfirmation ( ) ;
public function getPurchaseTotal ( ) ;

}

?>