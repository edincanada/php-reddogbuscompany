<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

class RdbcController implements RdbcIController
{

private $_dataStoreManager = NULL ,
        $_dataStore = NULL ,
        $_currentRequest = NULL ,
        $_currentResult = NULL ;

private function _initialize ( $pDataStoreManager )
{
  $this->_dataStoreManager = $pDataStoreManager ;
  $this->_currentRequest = NULL ;
  $this->_currentResult = NULL ;
  $this->_dataStore = NULL ;
}

private function _destroyAndReInitialize ( )
{
  $sessionToken = $this->_currentRequest->getSessionId() ;

  if ( $this->_dataStore != NULL )
  {
    $this->_dataStore->dispose() ;
    $this->_dataStore = NULL ;
    $this->_dataStoreManager->deleteDataStoreByToken( $sessionToken ) ;
  }

  $this->_dataStore = $this->_dataStoreManager->createDataStore() ;

  $result = new RdbcResult(
    RdbcResultType::Initialized ,
    $this->_dataStore->getTokenId() ,
    $this->_dataStore->getRoutesAssoc() ,
    NULL ,
    -1 ,
    false ,
    -1.0
  ) ;

  return $result ;
}

private function _processPurchaseSelection ( )
{
  $sessionToken = $this->_currentRequest->getSessionId() ;

  $validRequest = false ;
  $result = NULL ;

  $routesAssoc = NULL ;
  $routeIdsArray = NULL ;
  $routeSelected = NULL ;

  $ticketsSelected = -1 ;
  $ticketsAvailable = -1 ;
  $purchasePrice = -1.0 ;

  if ( $this->_dataStore != NULL )
  {
    $routesAssoc = $this->_dataStore->getRoutesAssoc() ;
    $routeIdsArray = array_keys( $routesAssoc ) ;

    if ( in_array( $this->_currentRequest->getRouteIdSelected() , $routeIdsArray ))
    {

      $routeSelected = $routesAssoc[ $this->_currentRequest->getRouteIdSelected() ] ;
      $ticketsSelected = $this->_currentRequest->getTicketsSelected() ;
      $ticketsAvailable = $routeSelected->getTicketsAvailable() ;

      if (( $ticketsSelected > 0 ) && ( $ticketsSelected <= $ticketsAvailable ))
      {

        $purchasePrice = $ticketsSelected * $routeSelected->getTicketPrice() ;

        $isValidTicketSelection = (
          $this->_currentRequest->getRequestType() == RdbcRequestType::SelectTickets &&
          !$this->_currentRequest->getPurchaseConfirmation()
        ) ;

        $isValidPurchaseConfirmation = (
          $this->_currentRequest->getRequestType() == RdbcRequestType::CompletePurchase &&
          $this->_currentRequest->getPurchaseConfirmation()
        ) ;

        $validRequest = $isValidTicketSelection || $isValidPurchaseConfirmation ;

        if ( $isValidPurchaseConfirmation )
        {

          $routeSelected->sellTickets( $ticketsSelected ) ;
          $this->_dataStore->updateBusRoute( $routeSelected ) ;

          $result = new RdbcResult(
            RdbcResultType::PurchaseComplete ,
            $sessionToken ,
            $routesAssoc ,
            $routeSelected->getRouteId() ,
            $ticketsSelected ,
            true ,
            $purchasePrice
          ) ;
        }
        else if ( $isValidTicketSelection )
        {
          $result = new RdbcResult(
            RdbcResultType::TicketsSelected ,
            $sessionToken ,
            $routesAssoc ,
            $routeSelected->getRouteId() ,
            $ticketsSelected ,
            false ,
            $purchasePrice
          ) ;
        }
      }
    }
  }

  if ( ! $validRequest )
  {
    $result = $this->_destroyAndReInitialize() ;
  }

  return $result ;
}

public function __construct ( $pDataStoreManager )
{
  $this->_initialize( $pDataStoreManager ) ;
}

public function RdbcController ( $pDataStoreManager )
{
  $this->_initialize( $pDataStoreManager ) ;
}

public function processRequest ( )
{
  $sessionToken = $this->_currentRequest->getSessionId() ;
  $this->_dataStore = $this->_dataStoreManager->getDataStore( $sessionToken ) ;

  switch ( $this->_currentRequest->getRequestType())
  {
    case ( RdbcRequestType::None ) :
    {
      $this->_currentResult = $this->_processError() ;
    } break ;

    case ( RdbcRequestType::Initialize ) :
    {
      $this->_currentResult = $this->_processInit() ;
    } break ;

    case ( RdbcRequestType::SelectRoute ) :
    {
      $this->_currentResult = $this->_processRouteSelected() ;
    } break ;

    case ( RdbcRequestType::SelectTickets ) :
    {
      $this->_currentResult = $this->_processTicketsSelected() ;
    } break ;

    case ( RdbcRequestType::CompletePurchase ) :
    {
      $this->_currentResult = $this->_processPurchasedConfirmed() ;
    } break ;

    default :
    {
      $this->_currentResult = $this->_processError() ;
    } break ;
  }

  if ( $this->_dataStore != NULL )
  {
    $this->_dataStore->dispose() ;
    $this->_dataStore = NULL ;
  }
}

private function _processInit ( )
{
  return $this->_destroyAndReInitialize() ;
}

private function _processRouteSelected ( )
{
  $result = NULL ;

  if ( $this->_dataStore == NULL )
  {
    $result = $this->_destroyAndReInitialize() ;
  }
  else
  {
    $sessionToken = $this->_currentRequest->getSessionId() ;
    $routesAssoc = $this->_dataStore->getRoutesAssoc() ;
    $routeIdsArray = array_keys( $routesAssoc ) ;
    $routeSelected = NULL ;

    if ( in_array( $this->_currentRequest->getRouteIdSelected() , $routeIdsArray ))
    {
      $routeSelected = $this->_currentRequest->getRouteIdSelected() ;
    }

    $result = new RdbcResult(
      RdbcResultType::RouteSelected ,
      $sessionToken ,
      $routesAssoc ,
      $routeSelected ,
      -1 ,
      false ,
      -1.0
    ) ;
  }

  return $result ;
}

private function _processTicketsSelected ( )
{
  return $this->_processPurchaseSelection() ;
}

private function _processPurchasedConfirmed ( )
{
  return $this->_processPurchaseSelection() ;
}

private function _processError ( )
{
  return $this->_destroyAndReInitialize() ;
}

public function setRequest ( $pRequest )
{
  $this->_currentRequest = $pRequest ;
  $this->_currentResult = NULL ;
}

public function getRequest ( )
{
  return $this->_currentRequest ;
}

public function getResult ( )
{
  return $this->_currentResult ;
}


}

?>