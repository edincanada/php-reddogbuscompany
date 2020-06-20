<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

class RdbcBusRoute
{

private $_routeId       = NULL ,
        $_origin        = NULL ,
        $_destination   = NULL ,
        $_tickePrice    = -1.0 ,
        $_routeCapacity = -1 ,
        $_ticketsSold   = -1 ;

private function _initialize (
  $pRouteId ,
  $pOrigin = '' ,
  $pDestination = ''  ,
  $pTicketPrice = 0.00 ,
  $pCapacity = 0 ,
  $pTicketsSold = 0
)
{
  $this->_routeId = "$pRouteId" ;
  $this->_origin = "$pOrigin" ;
  $this->_destination = "$pDestination" ;
  $this->_tickePrice = (double)( $pTicketPrice ) ;
  $this->_routeCapacity = (int)( $pCapacity ) ;
  $this->_ticketsSold = (int)( $pTicketsSold ) ;

  if ( $this->_ticketsSold > $this->_routeCapacity )
  {
    $this->_ticketsSold = $this->_routeCapacity ;
  }
}

public function __construct (
  $pRouteId ,
  $pOrigin = '' ,
  $pDestination = ''  ,
  $pTicketPrice = 0.00 ,
  $pCapacity = 0 ,
  $pTicketsSold = 0
)
{
  $this->_initialize (
    $pRouteId ,
    $pOrigin ,
    $pDestination ,
    $pTicketPrice ,
    $pCapacity ,
    $pTicketsSold
  ) ;
}

public function RdbcBusRoute (
  $pRouteId ,
  $pOrigin = '' ,
  $pDestination = ''  ,
  $pTicketPrice = 0.00 ,
  $pCapacity = 0 ,
  $pTicketsSold = 0
)
{
  $this->_initialize (
    $pRouteId ,
    $pOrigin ,
    $pDestination ,
    $pTicketPrice ,
    $pCapacity ,
    $pTicketsSold
  ) ;
}

public function getRouteId ( )
{
  return $this->_routeId ;
}

public function getOrigin ( )
{
  return $this->_origin ;
}

public function getDestination ( )
{
  return $this->_destination ;
}

public function getTicketPrice ( )
{
  return $this->_tickePrice ;
}

public function getRouteCapacity ( )
{
  return $this->_routeCapacity ;
}

public function getTicketsAvailable ( )
{
  return ( $this->_routeCapacity - $this->_ticketsSold ) ;
}

public function getTicketsSold ( )
{
  return $this->_ticketsSold ;
}

public function sellTicket ( )
{
  return $this->_sellTickets( 1 ) ;
}

public function sellTickets ( $pTicketsToSell )
{
  $retSuccess = false ;
  $ticketsToSell = (int)( $pTicketsToSell ) ;

  if ( $ticketsToSell > 0 )
  {
    if ( $this->_ticketsSold + $ticketsToSell <= $this->_routeCapacity )
    {
      $this->_ticketsSold += $ticketsToSell ;
      $retSuccess = true ;
    }
  }
  else if ( $ticketsToSell < 0 )
  {
    if ( $this->_ticketsSold >= $ticketsToSell )
    {
      $this->_ticketsSold -= $ticketsToSell ;
      $retSuccess = true ;
    }
  }
}

public function toDebugString ( )
{
  return (
    "[RdbcBusRoute(routeId: {$this->_routeId}), " .
    "(origin: {$this->_origin}), " .
    "(destination: {$this->_destination}), " .
    "(ticketPrice: {$this->_tickePrice}), " .
    "(capacity: {$this->_routeCapacity}), " .
    "(ticketsSold: {$this->_ticketsSold})]"
  ) ;
}

}

?>