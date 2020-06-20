<?php

class RdbcMysqlDataStoreManager implements RdbcIDataStoreManager
{

static private
  $_server = '' ,
  $_database = '' ,
  $_username = '' ,
  $_password = '',
  $_connectionString = 'mysql:host=%s;dbname=%s' ,
  $_createDatabaseQuery = 'SELECT RdbcCreateSession() AS sessionId' ,
  $_sessionIdResult = 'sessionId' ,
  $_sessionIdParam = ':sessionId' ,
  $_routeIdParam = ':routeId' ,
  $_ticketsParam = ':tickets' ,
  $_selectBusRoutesBySession =
    'SELECT * FROM viewRdbcRouteSessions WHERE sessionId LIKE :sessionId' ,
  $_deleteSession = 'SELECT RdbcDeleteSession( :sessionId ) AS RETURN_BOOL' ,
  $_updatetickets =
    'SELECT RdbcUpdateTickets( :sessionId , :routeId , :tickets ) AS RETURN_BOOL' ,
  $_boolResult = 'RETURN_BOOL' ,
  $_routeIdColumn = 'routeId' ,
  $_originColumn = 'origin' ,
  $_destinationColumn = 'destination' ,
  $_ticketPriceColumn = 'ticketPrice' ,
  $_seatsColumn = 'seats' ,
  $_seatsAvailableColumn = 'seatsAvailable' ;

private
  $_databaseHandler = NULL ,
  $_dataStoresAssoc = array() ;

public function open ( )
{
  if ( $this->_databaseHandler == NULL )
  {
    $this->_databaseHandler = new PDO(
     sprintf( self::$_connectionString , self::$_server , self::$_database ) ,
     self::$_username ,
     self::$_password
    ) ;
  }
}

public function isOpen ( )
{
  return (  $this->_databaseHandler != NULL ) ;
}

private function _retrieveBusRoutesAssoc ( $pSessionId )
{
  $retBusRoutesAssoc = array() ;

  $statementHandle =
    $this->_databaseHandler->prepare( self::$_selectBusRoutesBySession ) ;

  $statementHandle->bindParam( self::$_sessionIdParam , $pSessionId , PDO::PARAM_STR ) ;
  $statementHandle->execute() ;

  $currentRow = $statementHandle->fetch( PDO::FETCH_ASSOC ) ;

  while ( $currentRow != NULL )
  {
    $ticketsSold = (
      (int)( $currentRow[ self::$_seatsColumn ] ) -
      (int)( $currentRow[ self::$_seatsAvailableColumn ] )
    ) ;

    $retBusRoutesAssoc[ $currentRow[ self::$_routeIdColumn ]] =
      new RdbcBusRoute(
        $currentRow[ self::$_routeIdColumn ] ,
        $currentRow[ self::$_originColumn ] ,
        $currentRow[ self::$_destinationColumn ] ,
        (float)( $currentRow[ self::$_ticketPriceColumn ] ) ,
        $currentRow[ self::$_seatsColumn ] ,
        $ticketsSold
      ) ;

    $currentRow = $statementHandle->fetch( PDO::FETCH_ASSOC ) ;
  }

  return $retBusRoutesAssoc ;
}

public function createDataStore ( )
{
  $dataStore = NULL ;

  if ( $this->isOpen())
  {
    $statementHandle =
      $this->_databaseHandler->prepare( self::$_createDatabaseQuery ) ;

    $statementHandle->execute() ;
    $currentRow = $statementHandle->fetch( PDO::FETCH_ASSOC ) ;
    $sessionId = $currentRow[ self::$_sessionIdResult ] ;
    $dataStore = $this->getDataStore ( $sessionId ) ;
  }

  return $dataStore ;
}

public function getDataStore ( $pToken )
{
  $retDataStore = NULL ;
  $busRoutesAssoc = NULL ;

  if ( isset( $this->_dataStoresAssoc[ $pToken ] ))
  {
    $retDataStore = $this->_dataStoresAssoc[ $pToken ] ;
  }
  else if ( $this->isOpen())
  {
    $busRoutesAssoc = $this->_retrieveBusRoutesAssoc( $pToken ) ;

    if( count( $busRoutesAssoc ) > 0 )
    {
      $this->_dataStoresAssoc[ $pToken ] = new RdbcMysqlDataStore(
        $pToken ,
        $this ,
        $busRoutesAssoc
      ) ;

      $retDataStore = $this->_dataStoresAssoc[ $pToken ] ;
    }
  }

  return $retDataStore ;
}

public function deleteDataStoreByToken ( $pToken )
{
  if ( $this->isOpen())
  {
    $statementHandle =
      $this->_databaseHandler->prepare( self::$_deleteSession ) ;
    $statementHandle->bindParam( self::$_sessionIdParam , $pToken , PDO::PARAM_STR ) ;
    $statementHandle->execute() ;

    //$resultAssoc = $statementHandle->fetch( PDO::FETCH_ASSOC ) ;
    //$deleted = (boolean)( $resultAssoc[ self::$_boolResult ] ) ;

    if ( isset( $this->_dataStoresAssoc[ $pToken ] ))
    {
      $this->_dataStoresAssoc[ $pToken ]->dispose() ;
      $this->_dataStoresAssoc[ $pToken ] = NULL ;
      unset( $this->_dataStoresAssoc[ $pToken ] ) ;
    }
  }
}

public function updateTicketsForBusRoute ( $pToken , $pRouteId , $pTickets )
{
  if ( $this->isOpen())
  {
    $statementHandle =
      $this->_databaseHandler->prepare( self::$_updatetickets ) ;
    $statementHandle->bindParam( self::$_sessionIdParam , $pToken , PDO::PARAM_STR ) ;
    $statementHandle->bindParam( self::$_routeIdParam , $pRouteId , PDO::PARAM_STR ) ;
    $statementHandle->bindParam( self::$_ticketsParam , $pTickets , PDO::PARAM_INT ) ;

    $statementHandle->execute() ;
  }
}

public function close ( )
{
  $this->_databaseHandler = NULL ;
}

}

?>