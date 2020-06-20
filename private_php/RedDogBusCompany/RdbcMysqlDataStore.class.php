<?php

class RdbcMysqlDataStore implements RdbcIDataStore
{

private
  $_sessionId = NULL ,
  $_busRoutesAssoc = NULL ,
  $_manager = NULL ;

private function _initialize ( $pToken , $pManager , $pAssoc)
{
  $this->_sessionId = "{$pToken}" ;
  $this->_busRoutesAssoc = $pAssoc ;
  $this->_manager = $pManager ;
}

public function __construct ( $pToken , $pManager , $pAssoc )
{
  $this->_initialize( $pToken , $pManager , $pAssoc) ;
}

public function RdbcMysqlDataStore ( $pToken , $pManager , $pAssoc )
{
  $this->_initialize( $pToken , $pManager , $pAssoc ) ;
}

public function getTokenId ( )
{
  return $this->_sessionId ;
}

public function getRoutesAssoc ( )
{
  return $this->_busRoutesAssoc ;
}

public function updateBusRoute ( $pRoute )
{
  $matchingRoute = NULL ;
  if ( isset( $this->_busRoutesAssoc[ $pRoute->getRouteId() ] ))
  {
    $matchingRoute = $this->_busRoutesAssoc[ $pRoute->getRouteId() ] ;
    if ( $matchingRoute == $pRoute )
    {
      $this->_manager->updateTicketsForBusRoute(
        $this->_sessionId ,
        $pRoute->getRouteId() ,
        $pRoute->getTicketsAvailable()
      ) ;
    }
  }
}

public function updateBusRoutes ( $pBusRoutesArr )
{
  foreach( $pBusRoutesArr as $currentRoute )
  {
    $this->updateBusRoute( $currentRoute ) ;
  }
}

public function dispose ( )
{
  $this->_manager = NULL ;
}

}

?>
