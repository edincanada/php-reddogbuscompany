<?php

class RdbcJsonView implements RdbcIView
{

const
  REQUEST_METHOD_KEY = 'REQUEST_METHOD' ,
  GET_METHOD = 'GET' ,
  SESSION_KEY = 'sessionId' ,
  ROUTE_KEY = 'route' ,
  TICKETS_KEY = 'tickets' ,
  CONFIRMATION_KEY = 'purchase' ,
  CONFIRMATION_VALUE = 'true' ;

static private $_initializer = NULL ;

private
  $_request = NULL ,
  $_resultJsonString = NULL ;

static private function _getInitializer ( )
{
  if ( self::$_initializer == NULL )
  {
    self::$_initializer =
      new RdbcRequest( RdbcRequestType::Initialize , NULL , NULL , -1 , false ) ;
  }

  return self::$_initializer ;
}

private function _initialize ( $pArray )
{
  if( $_SERVER[ self::REQUEST_METHOD_KEY ] === self::GET_METHOD )
  {
    $this->_request = self::_getInitializer() ;
  }
  else
  {
    $session = $pArray[ self::SESSION_KEY ] ;
    $routeId = $pArray[ self::ROUTE_KEY ] ;
    $tickets = intval( $pArray[ self::TICKETS_KEY ] ) ;
    $purchaseConfirmation = true ;
      ( $pArray[ self::CONFIRMATION_KEY ] == self::CONFIRMATION_VALUE ) ;

    $this->_request = new RdbcRequest(
      RdbcRequestType::CompletePurchase ,
      $session ,
      $routeId ,
      $tickets ,
      $purchaseConfirmation
    ) ;
  }
}

public function __construct ( )
{
  $this->_initialize( $_POST ) ;
}

public function RdbcJsonView ( )
{
  $this->_initialize( $_POST ) ;
}

public function processResult( $pResult )
{
  $hashedRoutesAssoc = array() ;
  $ii = 0 ;
  foreach( $pResult->getRoutesAssoc() as $currentRoute )
  {
    $hashedRoutesAssoc[ $ii ] = array(
      'id'               => $currentRoute->getRouteId() ,
      'origin'           => $currentRoute->getOrigin() ,
      'destination'      => $currentRoute->getDestination() ,
      'ticketPrice'      => $currentRoute->getTicketPrice() ,
      'ticketsAvailable' => $currentRoute->getTicketsAvailable()
    ) ;

    $ii ++ ;
  }

  $this->_resultJsonString = json_encode(
    array(
      'type'                    => $pResult->getResultType() ,
      'sessionId'               => $pResult->getSessionId() ,
      'routeSelected'           => $pResult->getRouteIdSelected() ,
      'ticketsSelected'      => $pResult->getTicketsSelected() ,
      'purchaseConfirmation' => $pResult->getPurchaseConfirmation() ,
      'purchaseTotal'        => $pResult->getPurchaseTotal() ,
      'busRoutes'               => $hashedRoutesAssoc
    )
  ) ;
}

public function getRequest()
{
  return $this->_request ;
}

public function toJsonResponseString ( )
{
  return $this->_resultJsonString ;
}

}

?>