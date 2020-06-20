<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

class RdbcDebugDataStore implements RdbcIDataStore
{

private $_busRoutes = NULL ;

private function _initialize ( )
{
  $this->_busRoutes = array(
    '00' => new RdbcBusRoute(
      '00' ,
      'Toronto' ,
      'Montreal' ,
      '40.00' ,
      60 ,
      0
    ) ,
    '01' => new RdbcBusRoute(
      '01' ,
      'Toronto' ,
      'Ottawa' ,
      '17.25' ,
      60 ,
      0
    ) ,
    '02' => new RdbcBusRoute(
      '02' ,
      'Toronto' ,
      'Niagara' ,
      '11.50' ,
      60 ,
      0
    ) ,
    '03' => new RdbcBusRoute(
      '03' ,
      'Toronto' ,
      'Thunder Bay' ,
      '14.10' ,
      60 ,
      0
    )
  ) ;
}

public function __construct ( )
{
  $this->_initialize() ;
}

public function RdbcDebugDataStore ( )
{
  $this->_initialize() ;
}

public function getRoutesAssoc()
{
  return $this->_busRoutes ;
}

public function getTokenId ( )
{
  return 'DEBUG_TOKEN_ID' ;
}

public function updateBusRoute ( $pRoute )
{
  ;
}

public function updateBusRoutes ( $pRoutesAssoc )
{
  ;
}

public function dispose ( )
{
  ;
}

}

?>