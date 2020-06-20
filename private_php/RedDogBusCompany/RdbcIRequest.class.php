<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

final class RdbcRequestType
{

const
  None = 0 ,
  Initialize = 1 ,
  SelectRoute = 2 ,
  SelectTickets = 3 ,
  CompletePurchase = 4 ;

}

interface RdbcIRequest
{

public function getRequestType ( ) ;
public function getRouteIdSelected ( ) ;
public function getTicketsSelected ( ) ;
public function getPurchaseConfirmation ( ) ;

}

?>