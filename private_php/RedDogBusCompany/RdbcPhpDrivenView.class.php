<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

class RdbcPhpDrivenView implements RdbcIView
{

const SELECTED_ATTR = 'selected="selected"' ;
const CHECKED_ATTR = 'checked="checked"' ;
const DISABLED_ATTR = 'disabled="disabled"' ;
const FONT_WEIGHT_BOLD_CLASS_ATTR = 'class="cssWeightBold"' ;
const PURCHASE_STATUS_IN_PROGRESS_CSS = 'cssDivPurchaseInProgress' ;
const PURCHASE_STATUS_COMPLETE_CSS = 'cssDivPurchaseComplete' ;
const SELECT_ROUTE_ID = 'sltRouteSelection' ;
const RADIO_TICKETS_NAME = 'rblNumberOfTicketsToPurchase' ;
const RADIO_TICKETS_ID_PREFIX = 'rbtNumberOfTicketsToPurchase_' ;
const BUTTON_PURCHASE_ID = 'btnPurchase' ;
const BUTTON_PURCHASE_VALUE = 'Purchase' ;
const OPTION_SELECT_ROUTE_ID_PREFIX = 'optSelectRoute_' ;
const HTML_BR = "<br />\n" ;
const EMPTY_CELL_ELEMENT = '<td>&nbsp;</td>' ;
const TICKET_BUTTONS_PER_ROW = 10 ;
const SENDER_ID_ID = 'hdnPostBackSenderId' ;
const SENDER_VALUE_ID = 'hdnPostBackSenderValue' ;
const SESSION_TOKEN_ID = 'hdnSessionToken' ;
const COL_SPAN_TEN_ATTR = 'colspan="10"' ;

static private
  $_ticketButtonFormat = '<input id="%s" name="%s" type="radio" value="%s"%s />' ,
  $_ticketLabelFormat = '<label for="%s">%02d</label>' ,
  $_routeInfoRowsFormat = '<tr%s><td>%s</td><td>%0.2lf</td><td>%02d</td></tr>' ,
  $_busRouteInfoDisplayText = 'Route %s - %s to %s' ,
  $_routeOptionFormat = '<option id="%s" value="%s"%s%s>%s</option>' ,
  $_priceInfoDisplayFormat = '%02d X $%0.2lf = $%0.2lf' ,
  $_selectRouteOptionFormat =
    '<option id="optNoRouteSelected" value=""%s>Select a route</option>' ,
  $_statusMessageAssoc = array(
    RdbcResultType::Initialized => 'Red Dog Bus Company Ticketing System' ,
    RdbcResultType::RouteSelected => 'Route selected' ,
    RdbcResultType::TicketsSelected => 'Tickets selected' ,
    RdbcResultType::PurchaseComplete => 'Purchase complete'
  ) ,
  $_noRouteSelectedRow =
    '<tr><td>Please select a route</td></tr>' ,
  $_hiddenSenderIdElement =
    '<input id="hdnPostBackSenderId" name="hdnPostBackSenderId" type="hidden" value="" />' ,
  $_hiddenSenderValueElement =
    '<input id="hdnPostBackSenderValue" name="hdnPostBackSenderValue" type="hidden" value="" />' ,
  $_hiddenSessionTokenFormat =
    '<input id="hdnSessionToken" name="hdnSessionToken" type="hidden" value="%s" />' ,
  $_initializer = NULL ;

private $_routesDisplayTextAssoc = NULL ,
        $_routeOptionsAssoc = NULL ,
        $_noOptionSelectedAttr = NULL ,
        $_ticketButtonsArray = NULL ,
        $_routeInfoRowsAssoc = NULL ,
        $_purchaseStatusMessage = NULL ,
        $_purchaseStatusStyle = NULL ,
        $_purchaseButtonDisabledAttr = NULL ,
        $_sessionToken = NULL ,
        $_request = NULL ,
        $_stepTwoHeaderColSpanAttr = NULL ;

private function _initialize ( )
{
  $this->_routesDisplayTextAssoc = '' ;
  $this->_routeOptionsAssoc = NULL ;
  $this->_noOptionSelectedAttr = '' ;
  $this->_ticketButtonsArray = NULL ;
  $this->_routeInfoRowsAssoc = NULL ;
  $this->_purchaseStatusMessage = '' ;
  $this->_purchaseStatusStyle = '' ;
  $this->_purchaseButtonDisabledAttr = NULL ;
  $this->_sessionToken = '' ;
  $this->_stepTwoHeaderColSpanAttr = '' ;

  $this->_initializeRequest( $_POST ) ;
}

private function _initializeRequest ( $pArray )
{
  $request = NULL ;


  if ( ! array_key_exists( self::SENDER_ID_ID , $pArray ))
  {
    $request = self::_getInitializer() ;
  }
  else
  {
    $routeId = $pArray[ self::SELECT_ROUTE_ID ] ;

    $tickets = -1 ;
    if ( array_key_exists( self::RADIO_TICKETS_NAME , $pArray ))
    {
      $tickets = intval( $pArray[ self::RADIO_TICKETS_NAME ] ) ;
    }

    $session = $pArray[ self::SESSION_TOKEN_ID ] ;
    $purchaseConfirmation = false ;
    $requestType = NULL ;
    $sender = $pArray[ self::SENDER_ID_ID ] ;

    switch ( $sender )
    {
      case NULL :
      {
        $requestType = RdbcRequestType::Initialize ;
      } break ;

      case self::SELECT_ROUTE_ID :
      {
        $requestType = RdbcRequestType::SelectRoute ;
      } break ;

      case self::RADIO_TICKETS_NAME :
      {
        $requestType = RdbcRequestType::SelectTickets ;
      } break ;

      case self::BUTTON_PURCHASE_ID :
      {
        $requestType = RdbcRequestType::CompletePurchase ;
        $purchaseConfirmation =
          ( $pArray[ self::BUTTON_PURCHASE_ID ] == self::BUTTON_PURCHASE_VALUE ) ;

      } break ;

      default :
      {
        $requestType = RdbcRequestType::Initialize ;
      } break ;
    }

    $request = new RdbcRequest(
      $requestType ,
      $session ,
      $routeId ,
      $tickets ,
      $purchaseConfirmation
    ) ;
  }

  $this->_request = $request ;
}

static private function _getInitializer ( )
{
  if ( self::$_initializer == NULL )
  {
    self::$_initializer =
      new RdbcRequest( RdbcRequestType::Initialize , NULL , NULL , -1 , false ) ;
  }

  return self::$_initializer ;
}

private function _generateRouteInfoRows ( $pBusRoutesAssoc , $pSelectedRouteId )
{
  $this->_routeInfoRowsAssoc = array() ;
  $boldClassAttribute = '' ;

  foreach ( $pBusRoutesAssoc as $currentBusRoute )
  {
    $boldClassAttribute = '' ;
    if ( $currentBusRoute->getRouteId() == $pSelectedRouteId )
    {
      $boldClassAttribute = ' ' . self::FONT_WEIGHT_BOLD_CLASS_ATTR ;
    }

    $this->_routeInfoRowsAssoc[ $currentBusRoute->getRouteId() ] =
      sprintf(
        self::$_routeInfoRowsFormat ,
        $boldClassAttribute ,
        $this->_routesDisplayTextAssoc[ $currentBusRoute->getRouteId() ] ,
        $currentBusRoute->getTicketPrice() ,
        $currentBusRoute->getTicketsAvailable()
      ) ;
  }
}

private function _generateRouteDisplayText ( $pBusRoutesAssoc )
{
  $this->_routesDisplayTextAssoc = array() ;

  foreach ( $pBusRoutesAssoc as $currentBusRoute )
  {
    $this->_routesDisplayTextAssoc[ $currentBusRoute->getRouteId() ] =
      sprintf(
        self::$_busRouteInfoDisplayText ,
        $currentBusRoute->getRouteId() ,
        $currentBusRoute->getOrigin() ,
        $currentBusRoute->getdestination()
      ) ;
  }
}

private function _generateRouteOptions ( $pBusRoutesAssoc , $pSelectedRouteId )
{
  $this->_routeOptionsAssoc = array() ;
  $currentId = NULL ;
  $disabledAttribute = '' ;
  $selectedAttribute = '' ;
  $isRouteSelected = false ;

  foreach ( $pBusRoutesAssoc as $currentBusRoute )
  {
    $currentId = $currentBusRoute->getRouteId() ;
    $disabledAttribute = '' ;
    $selectedAttribute = '' ;

    if ( $currentBusRoute->getTicketsAvailable() < 1 )
    {
      $disabledAttribute = ' ' . self::DISABLED_ATTR ;
    }
    else if ( $currentId == $pSelectedRouteId )
    {
      $isRouteSelected = true ;
      $selectedAttribute = ' ' . self::SELECTED_ATTR ;
    }

    $this->_routeOptionsAssoc[ $currentId ] =
      sprintf(
        self::$_routeOptionFormat ,
        self::OPTION_SELECT_ROUTE_ID_PREFIX . $currentId ,
        $currentId ,
        $disabledAttribute ,
        $selectedAttribute ,
        $this->_routesDisplayTextAssoc[ $currentId ]
      ) ;
  }

  if ( ! $isRouteSelected )
  {
    $this->_noOptionSelectedAttr = ' ' . self::SELECTED_ATTR ;
  }
}

private function _generateTicketButtons ( $pButtonCount , $pSelectedButton )
{
  $this->_ticketButtonsArray = array() ;
  $labelElement = '' ;
  $buttonElement = '' ;
  $checkedAttribute = '' ;

  for ( $ii = 1 ; $ii <= $pButtonCount ; $ii ++ )
  {
    $checkedAttribute = '' ;
    if ( $pSelectedButton == $ii )
    {
      $checkedAttribute = ' ' . self::CHECKED_ATTR ;
    }

    $buttonElement =
      sprintf(
        self::$_ticketButtonFormat ,
        ( self::RADIO_TICKETS_ID_PREFIX . $ii ) ,
        self::RADIO_TICKETS_NAME ,
        $ii ,
        $checkedAttribute
      ) ;

    $labelElement =
      sprintf(
        self::$_ticketLabelFormat ,
        ( self::RADIO_TICKETS_ID_PREFIX . $ii ) ,
        $ii
      ) ;

    $this->_ticketButtonsArray[ $ii - 1 ] = "<td>{$buttonElement}{$labelElement}</td>" ;
  }

  $ii = count( $this->_ticketButtonsArray ) ;
  $remainder = self::TICKET_BUTTONS_PER_ROW - ( $ii % self::TICKET_BUTTONS_PER_ROW ) ;

  for ( $jj = 0 ; $jj < $remainder ; $jj ++ )
  {
    $this->_ticketButtonsArray[ $ii ] = self::EMPTY_CELL_ELEMENT ;
    $ii ++ ;
  }

  if ( count( $this->_ticketButtonsArray ) > 0 )
  {
    $this->_stepTwoHeaderColSpanAttr = ' ' . self::COL_SPAN_TEN_ATTR ;
  }
}

private function _processInitialized ( $pResult )
{
  $busRoutesAssoc = $pResult->getRoutesAssoc() ;

  $this->_generateRouteDisplayText( $busRoutesAssoc ) ;
  $this->_generateRouteInfoRows( $busRoutesAssoc , NULL ) ;
  $this->_generateRouteOptions( $busRoutesAssoc , -1 ) ;

  $this->_purchaseButtonDisabledAttr = ' ' . self::DISABLED_ATTR ;
  $this->_purchaseStatusStyle = self::PURCHASE_STATUS_IN_PROGRESS_CSS ;

  $this->_purchaseStatusMessage =
    self::$_statusMessageAssoc[ RdbcResultType::Initialized ] ;

  $this->_sessionToken = $pResult->getSessionId() ;
}

private function _processRouteSelected ( $pResult )
{
  $busRoutesAssoc = $pResult->getRoutesAssoc() ;
  $routeIdSeleted = $pResult->getRouteIdSelected() ;
  $routeSelected = NULL ;

  if ( $routeIdSeleted != NULL )
  {
    $routeSelected = $busRoutesAssoc[ $routeIdSeleted ] ;
  }

  $this->_generateRouteDisplayText( $busRoutesAssoc ) ;
  $this->_generateRouteInfoRows( $busRoutesAssoc , $routeIdSeleted ) ;
  $this->_generateRouteOptions( $busRoutesAssoc , $routeIdSeleted ) ;

  if ( $routeSelected != NULL )
  {
    $this->_generateTicketButtons( $routeSelected->getTicketsAvailable() , -1 ) ;
    $this->_purchaseStatusMessage =
      self::$_statusMessageAssoc[ RdbcResultType::RouteSelected ] . ': ' .
      $this->_routesDisplayTextAssoc[ $routeIdSeleted ] ;
  }
  else
  {
    $this->_purchaseStatusMessage =
      self::$_statusMessageAssoc[ RdbcResultType::Initialized ] ;
  }

  $this->_purchaseButtonDisabledAttr = ' ' . self::DISABLED_ATTR ;
  $this->_purchaseStatusStyle = self::PURCHASE_STATUS_IN_PROGRESS_CSS ;
  $this->_sessionToken = $pResult->getSessionId() ;
}

private function _processTicketsSelected ( $pResult )
{
  $busRoutesAssoc = $pResult->getRoutesAssoc() ;
  $routeIdSeleted = $pResult->getRouteIdSelected() ;
  $routeSelected = $busRoutesAssoc[ $routeIdSeleted ] ;

  $this->_generateRouteDisplayText( $busRoutesAssoc ) ;
  $this->_generateRouteInfoRows( $busRoutesAssoc , $routeIdSeleted ) ;
  $this->_generateRouteOptions( $busRoutesAssoc , $routeIdSeleted ) ;
  $this->_generateTicketButtons(
    $routeSelected->getTicketsAvailable() ,
    $pResult->getTicketsSelected()
  ) ;

  $this->_purchaseButtonDisabledAttr = '' ;
  $this->_purchaseStatusStyle = self::PURCHASE_STATUS_IN_PROGRESS_CSS ;

  $this->_purchaseStatusMessage =
    self::$_statusMessageAssoc[ RdbcResultType::TicketsSelected ] . ': ' .
    sprintf(
      self::$_priceInfoDisplayFormat ,
      $pResult->getTicketsSelected() ,
      $routeSelected->getTicketPrice() ,
      $pResult->getPurchaseTotal()
    ) .
    self::HTML_BR .
    self::$_statusMessageAssoc[ RdbcResultType::RouteSelected ] . ': ' .
    $this->_routesDisplayTextAssoc[ $routeIdSeleted ] ;

  $this->_sessionToken = $pResult->getSessionId() ;
}

private function _processPurchaseComplete ( $pResult )
{
  $busRoutesAssoc = $pResult->getRoutesAssoc() ;
  $routeIdSeleted = $pResult->getRouteIdSelected() ;
  $routeSelected = $busRoutesAssoc[ $routeIdSeleted ] ;

  $this->_generateRouteDisplayText( $busRoutesAssoc ) ;

  if ( $routeSelected->getTicketsAvailable() > 0 )
  {
    $this->_generateRouteInfoRows( $busRoutesAssoc , $routeIdSeleted ) ;
    $this->_generateRouteOptions( $busRoutesAssoc , $routeIdSeleted ) ;
    $this->_generateTicketButtons(
      $routeSelected->getTicketsAvailable() ,
      -1
    ) ;
  }
  else
  {
    $this->_generateRouteInfoRows( $busRoutesAssoc , NULL ) ;
    $this->_generateRouteOptions( $busRoutesAssoc , NULL ) ;
  }

  $this->_purchaseButtonDisabledAttr = ' ' . self::DISABLED_ATTR ;
  $this->_purchaseStatusStyle = self::PURCHASE_STATUS_COMPLETE_CSS ;

  $this->_purchaseStatusMessage =
    self::$_statusMessageAssoc[RdbcResultType::PurchaseComplete] . self::HTML_BR .
    self::$_statusMessageAssoc[ RdbcResultType::TicketsSelected ] . ': ' .
    sprintf(
      self::$_priceInfoDisplayFormat ,
      $pResult->getTicketsSelected() ,
      $routeSelected->getTicketPrice() ,
      $pResult->getPurchaseTotal()
    ) .
    self::HTML_BR .
    self::$_statusMessageAssoc[ RdbcResultType::RouteSelected ] . ': ' .
    $this->_routesDisplayTextAssoc[ $routeIdSeleted ] ;

    $this->_sessionToken = $pResult->getSessionId() ;
}

public function __construct ( )
{
  $this->_initialize() ;
}

public function RdbcPhpDrivenView ( )
{
  $this->_initialize() ;
}

public function getRequest ( )
{
  return $this->_request ;
}

public function processResult ( $pResult )
{
  switch ( $pResult->getResultType() )
  {
    case RdbcResultType::Initialized :
    {
      $this->_processInitialized( $pResult ) ;
    } break ;

    case RdbcResultType::RouteSelected :
    {
      $this->_processRouteSelected( $pResult ) ;
    } break ;

    case RdbcResultType::TicketsSelected :
    {
      $this->_processTicketsSelected( $pResult ) ;
    } break ;

    case RdbcResultType::PurchaseComplete :
    {
      $this->_processPurchaseComplete( $pResult ) ;
    } break ;

    default :
    {
      $this->_processInitialized( $pResult ) ;
    } break ;
  }
}

static private function _printIndentedHtmlArray (
  $pArray ,
  $pTabSize = 1 ,
  $pIndentfirstLine = false
)
{
  $indentation = sprintf(( '%' . "{$pTabSize}" . 's' ) , '' ) ;
  $currentIndentation = '' ;
  
  if ( $pIndentfirstLine )
  {
    $currentIndentation = $indentation ;
  }

  foreach ( $pArray as $currentElement )
  {
    echo $currentIndentation , $currentElement , "\n" ;
    $currentIndentation = $indentation ;
  }
}

public function printRouteInfoRows ( )
{
  self::_printIndentedHtmlArray( $this->_routeInfoRowsAssoc , 2 ) ;
}

public function printRouteOptions ( )
{
  echo sprintf( self::$_selectRouteOptionFormat , $this->_noOptionSelectedAttr ) , "\n" ;
  self::_printIndentedHtmlArray( $this->_routeOptionsAssoc , 3 , true ) ;
}

public function printColSpanAttribute ( )
{
  echo $this->_stepTwoHeaderColSpanAttr ;
}

public function printTicketRadioButtons ( )
{
  $currentButton = 0 ;
  $ticketButtonRowsArray = array() ;

  $rowCount = count( $this->_ticketButtonsArray ) / self::TICKET_BUTTONS_PER_ROW ;
  $currentRow = '' ;

  if ( count( $this->_ticketButtonsArray ) > 0 )
  {
    for ( $ii = 0 ; $ii < $rowCount ; $ii ++ )
    {
      $currentRow = '' ;
      for ( $jj = 0 ; $jj < self::TICKET_BUTTONS_PER_ROW ; $jj ++ )
      {
        $currentRow = $currentRow . $this->_ticketButtonsArray[ $currentButton ] ;
        $currentButton ++ ;
      }

      $ticketButtonRowsArray[ $ii ] = '<tr>' . $currentRow . '</tr>' ;
    }
  }
  else
  {
    $ticketButtonRowsArray[ 0 ] = self::$_noRouteSelectedRow ;
  }

  self::_printIndentedHtmlArray( $ticketButtonRowsArray , 3 ) ;
}

public function printButtonDisabledAttribute ( )
{
  echo $this->_purchaseButtonDisabledAttr ;
}

public function printPuchaseStatusStyle ( )
{
  echo $this->_purchaseStatusStyle ;
}

public function printPurchaseStatusText ( )
{
  echo $this->_purchaseStatusMessage ;
}

public function printPostbackControlElements ( )
{
  self::_printIndentedHtmlArray(
    array(
      self::$_hiddenSenderIdElement ,
      self::$_hiddenSenderValueElement ,
      sprintf( self::$_hiddenSessionTokenFormat , $this->_sessionToken )
    ) ,
    1
  ) ;
}

}

?>