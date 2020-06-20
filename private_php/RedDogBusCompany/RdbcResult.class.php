<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

class RdbcResult implements RdbcIResult
{

private $_type = RdbcResultType::None ,
        $_sessionId = NULL ,
        $_routesAssoc = NULL ,
        $_routeId = NULL ,
        $_tickets = -1 ,
        $_confirmation = false ,
        $_total = -1.0 ;

static private function _isAssociativeArray ( $pArray )
{
  return (
     is_array( $pArray ) &&
     array_diff_key( $pArray , array_keys( array_keys( $pArray )))
  ) ;
}

private function _initialize (
  $pResultType = RdbcResultType::None ,
  $pSessionId = NULL ,
  $pRoutesAssoc = NULL ,
  $pRouteId = NULL ,
  $pTicketsSelected = -1 ,
  $pConfirmation = false ,
  $pTotal = -1.0
)
{
  $this->_sessionId = "$pSessionId" ;

  $this->_type = (int)( $pResultType ) ;

  if (( $this->_type < RdbcResultType::None ) ||
      ( $this->_type > RdbcResultType::PurchaseComplete ))
  {
    $this->_type = RdbcResultType::None ;
  }

  $this->_routesAssoc = $pRoutesAssoc ;

  if ( !RdbcResult::_isAssociativeArray( $this->_routesAssoc ))
  {
    $this->_routesAssoc = array() ;
  }

  $this->_routeId = "$pRouteId" ;
  $this->_tickets = (int)( $pTicketsSelected ) ;
  $this->_confirmation = (bool)( $pConfirmation ) ;
  $this->_total = (double)( $pTotal ) ;
}

public function __construct (
  $pResultType ,
  $pSessionId ,
  $pRoutesAssoc ,
  $pRouteId ,
  $pTicketsSelected ,
  $pConfirmation ,
  $pTotal
)
{
  $this->_initialize(
    $pResultType ,
    $pSessionId ,
    $pRoutesAssoc ,
    $pRouteId ,
    $pTicketsSelected ,
    $pConfirmation ,
    $pTotal
  ) ;
}

public function RdbcResult (
  $pResultType ,
  $pSessionId ,
  $pRoutesAssoc ,
  $pRouteId ,
  $pTicketsSelected ,
  $pConfirmation ,
  $pTotal
)
{
  $this->_initialize(
    $pResultType ,
    $pSessionId ,
    $pRoutesAssoc ,
    $pRouteId ,
    $pTicketsSelected ,
    $pConfirmation ,
    $pTotal
  ) ;
}

public function getResultType ( )
{
  return $this->_type ;
}

public function getSessionId ( )
{
  return $this->_sessionId ;
}

public function getRouteIdSelected ( )
{
  return $this->_routeId ;
}

public function getRoutesAssoc ( )
{
  return $this->_routesAssoc ;
}

public function getTicketsSelected ( )
{
  return $this->_tickets ;
}

public function getPurchaseConfirmation ( )
{
  return $this->_confirmation ;
}

public function getPurchaseTotal ( )
{
  return $this->_total ;
}

public function toDebugString ( )
{
  $retString = (
    "[RdbcResult(type: {$this->_type}), " .
    "(routeId: {$this->_routeId}), " .
    "(tickets: {$this->_tickets}), " .
    "(confirmation: {$this->_confirmation}), " .
    "(total: {$this->_total}), " .
    "(routes: "
  ) ;

  $commaSpace = '' ;

  foreach( $this->_routesAssoc as $routeId => $route )
  {
    $retString =
      $retString .
      $commaSpace .
      "($routeId: " .
      $route->toDebugString() .
      ')' ;

    $commaSpace = ', ' ;
  }

  $retString = $retString . ")]" ;

  return $retString ;
}

}

?>