<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

class RdbcRequest implements RdbcIRequest
{

private $_requestType = RdbcRequestType::None ,
        $_sessionId = NULL ,
        $_routeIdSelected = NULL ,
        $_ticketsSelected = -1 ,
        $_purchaseConfirmation = false ;

private function _initialize (
  $pType = RdbcRequestType::None ,
  $pSessionId = NULL ,
  $pRouteId = NULL ,
  $pTickets = -1 ,
  $pConfirmation = false
)
{
  $this->_requestType = (int)( $pType ) ;

  if (( $this->_requestType < RdbcRequestType::None ) ||
      ( $this->_requestType > RdbcRequestType::CompletePurchase ))
  {
     $this->_requestType = RdbcRequestType::None ;
  }

  $this->_sessionId = "$pSessionId" ;
  $this->_routeIdSelected = "$pRouteId" ;
  $this->_ticketsSelected = (int)( $pTickets ) ;
  $this->_purchaseConfirmation = (bool)( $pConfirmation ) ;
}

public function __construct (
  $pType ,
  $pSessionId ,
  $pRouteId ,
  $pTickets ,
  $pConfirmation
)
{
  $this->_initialize(
    $pType ,
    $pSessionId ,
    $pRouteId ,
    $pTickets ,
    $pConfirmation
  ) ;
}

public function RdbcRequest (
  $pType ,
  $pSessionId ,
  $pRouteId ,
  $pTickets ,
  $pConfirmation
)
{
  $this->_initialize(
    $pType ,
    $pSessionId ,
    $pRouteId ,
    $pTickets ,
    $pConfirmation
  ) ;
}

public function getRequestType ( )
{
  return $this->_requestType ;
}

public function getSessionId ( )
{
  return $this->_sessionId ;
}

public function getRouteIdSelected ( )
{
  return $this->_routeIdSelected ;
}

public function getTicketsSelected ( )
{
  return $this->_ticketsSelected ;
}

public function getPurchaseConfirmation ( )
{
  return $this->_purchaseConfirmation ;
}

public function toDebugString ( )
{
  return (
    "[RdbcRequest(type: {$this->_requestType} ), " .
    "(sessionId: {$this->_sessionId}), " .
    "(routeId: {$this->_routeIdSelected}), " .
    "(tickets: {$this->_ticketsSelected}), " .
    "(confirmation: {$this->_purchaseConfirmation})]"
  ) ;
}

}

?>
