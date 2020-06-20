( function ( )
{

var J_DIV_PAGE_ROUTE_SELECTION_ID   = '#divPageRouteSelection' ,
    J_DIV_PAGE_PURCHASE_ID          = '#divPagePurchase' ,
    J_DIV_PAGE_CONFIRM_PURCHASE_ID  = '#divPageConfirmPurchase' ,
    J_DIV_PAGE_PURCHASE_COMPLETE_ID = '#divPagePurchaseComplete' ,
    J_UL_ROUTES_ID                  = '#ulRoutes' ,
    J_FORM_TICKET_SELECTION_ID      = '#formTicketSelection' ,
    J_SPAN_ROUTE_SELECTED_ID        = '#spanRouteSelected' ,
    J_SPAN_TICKET_PRICE_ID          = '#spanTicketPrice' ,
    J_SPAN_TICKETS_AVAILABLE_ID     = '#spanTicketsAvailable' ,
    J_INP_TICKET_SLIDER_ID          = '#inpTicketSlider' ,
    J_SPAN_PURCHASE_PRICE_ID        = '#spanPurchasePrice' ,
    J_BTN_PURCHASE_ID               = '#btnPurchase' ,
    J_ANC_PURCHASE_ID               = '#ancPurchase' ,
    J_SPAN_CONFIRM_TICKETS_ID       = '#spanConfirmTickets' ,
    J_SPAN_CONFIRM_PRICE_ID         = '#spanConfirmPrice' ;


var divPageRouteSelection = null ,
    divPagePurchase = null ,
    divPageConfirmPurchase = null ,
    divPagePurchaseComplete = null ,
    ulRoutes = null ,
    formTicketSelection = null ,
    btnPurchase = null ,
    ancPurchase = null ,
    spanConfirmTickets = null ,
    spanConfirmPrice = null ;

var busRoutes = null , sessionId = null , routeSelected = null , loadingWidgetOn = false ;

var ENTER_KEY = 13 ;

var preventSubmissionOnEnter = function ( pEvent )
{
  var goOnWithEvent = true ;

  if ( pEvent.keyCode == ENTER_KEY )
  {
    pEvent.preventDefault() ;
    goOnWithEvent = false ;
  }

  return goOnWithEvent ;
} ;

var preventLongClickDefault = function ( pEvent )
{
  pEvent.preventDefault() ;
  pEvent.stopPropagation() ;
  return false ;
} ;


var toCurrency = function ( pNumber )
{
  return parseFloat( Math.round( pNumber * 100) / 100 ).toFixed( 2 ) ;
} ;

var main = function ( )
{
  divPageRouteSelection   = $( J_DIV_PAGE_ROUTE_SELECTION_ID ) ;
  divPagePurchase         = $( J_DIV_PAGE_PURCHASE_ID ) ;
  divPageConfirmPurchase  = $( J_DIV_PAGE_CONFIRM_PURCHASE_ID ) ;
  divPagePurchaseComplete = $( J_DIV_PAGE_PURCHASE_COMPLETE_ID ) ;
  ulRoutes                = $( J_UL_ROUTES_ID ) ;
  spanRouteSelected       = $( J_SPAN_ROUTE_SELECTED_ID ) ;
  spanTicketPrice         = $( J_SPAN_TICKET_PRICE_ID ) ;
  spanTicketsAvailable    = $( J_SPAN_TICKETS_AVAILABLE_ID ) ;
  formTicketSelection     = $( J_FORM_TICKET_SELECTION_ID ) ;
  inpTicketSlider         = $( J_INP_TICKET_SLIDER_ID ) ;
  spanPurchasePrice       = $( J_SPAN_PURCHASE_PRICE_ID ) ;
  btnPurchase             = $( J_BTN_PURCHASE_ID ) ;
  ancPurchase             = $( J_ANC_PURCHASE_ID ) ;
  spanConfirmTickets      = $( J_SPAN_CONFIRM_TICKETS_ID ) ;
  spanConfirmPrice        = $( J_SPAN_CONFIRM_PRICE_ID ) ;

  ancPurchase.on( 'click' , onPurchaseConfirmed ) ;

  divPageRouteSelection.one( 'pagebeforeshow' , beforeshowDivPageRouteSelectionOnce ) ;
  divPagePurchase.one( 'pagebeforeshow' , beforeShowDivPagePurchaseOnce ) ;
  divPagePurchase.on( 'pagebeforeshow' , beforeShowDivPagePurchaseAlways ) ;

  $( document ).on( 'contextmenu' , 'a' , preventLongClickDefault ) ;
  $( document ).on( 'contextmenu' , '.ui-slider' , preventLongClickDefault ) ;
} ;

var beforeshowDivPageRouteSelectionOnce = function ( )
{
  loadRoutes( populateRouteList ) ;
} ;

var beforeShowDivPagePurchaseOnce = function ( )
{
  formTicketSelection.on( 'keypress' , preventSubmissionOnEnter ) ;
  formTicketSelection.on( 'change' , J_INP_TICKET_SLIDER_ID , onTicketsSelectedChange ) ;
} ;

var beforeShowDivPagePurchaseAlways = function ( )
{
  populateRouteInformation( routeSelected ) ;
} ;

var _CSS_UI_DISABLED = 'ui-disabled' ;
var onTicketsSelectedChange = function ( pEvent )
{
  var currentValue = parseInt( $(this).val()) ;

  if ( isNaN( currentValue ))
  {
    currentValue = 0 ;
    $(this).val( '0' ) ;
  }
  if (( currentValue < 1 ) ||
      ( currentValue > parseFloat( routeSelected.ticketsAvailable )))
  {
    btnPurchase.addClass( _CSS_UI_DISABLED ) ;
  }
  else
  {
    btnPurchase.removeClass( _CSS_UI_DISABLED ) ;
  }

  var priceText = toCurrency( parseFloat( routeSelected.ticketPrice ) * currentValue ) ;

  spanConfirmTickets.text( currentValue ) ;
  spanConfirmPrice.text( priceText ) ;

  spanPurchasePrice.text( priceText ) ;
} ;

var purchaseSuccessHandler = function ( pBusRoute )
{
  populateRouteList( pBusRoute ) ;
  setTimeout(
    function ( )
    {
      divPagePurchaseComplete.popup( 'open' )
    } ,
    100
  ) ;
} ;

var onPurchaseConfirmed = function ( )
{
  divPageConfirmPurchase.one(
    'popupafterclose' ,
    showLoadingWidget
  ) ;

  purchaseTickets(
    routeSelected ,
    parseInt( $( J_INP_TICKET_SLIDER_ID ).val()) ,
    purchaseSuccessHandler
  ) ;
} ;


var _A_TAG = '<a />' , _LI_TAG = '<li />' ;

var setRouteSelectedHandler = function ( pBusRoute )
{
  return function ( )
  {
    routeSelected = pBusRoute ;
  }
}

var populateRouteList = function ( pBusArray )
{
  var ii = 0 , newAnchor, newLi ;
  ulRoutes.empty() ;

  for( ii = 0 ; ii < pBusArray.length ; ii++ )
  {
    newAnchor = $(
      _A_TAG ,
      {
        'href' : J_DIV_PAGE_PURCHASE_ID ,
        'data-transition' : 'slide' ,
        'html' : pBusArray[ ii ].origin + ' to ' + pBusArray[ ii ].destination
      }
    ) ;

    newAnchor.on( 'click' , setRouteSelectedHandler( pBusArray[ ii ] )) ;

    newLi = $( _LI_TAG ) ;
    newLi.append( newAnchor ) ;
    ulRoutes.append( newLi ) ;
  }

  ulRoutes.listview( 'refresh' ) ;
} ;

var populateRouteInformation = function ( pBusRoute )
{
  $( J_INP_TICKET_SLIDER_ID ).val( '0' ) ;
  $( J_INP_TICKET_SLIDER_ID ).slider( 'refresh' ) ;
  spanRouteSelected.text( pBusRoute.origin + ' to ' + pBusRoute.destination ) ;
  spanTicketPrice.text( toCurrency( parseFloat( pBusRoute.ticketPrice ))) ;
  spanTicketsAvailable.text( pBusRoute.ticketsAvailable ) ;

} ;

var _SERVICE_URL = '/apps/reddogbuscompany/mobile/service.php' ;
var loadRoutes = function ( pCallBack )
{
  showLoadingWidget() ;
  $.ajax({
    url : _SERVICE_URL ,
    dataType : 'json' ,
    success : function ( pData )
    {
      busRoutes = pData.busRoutes ;
      sessionId = pData.sessionId ;
      pCallBack( busRoutes ) ;
      hideLoadingWidget() ;

      console.log( pData.sessionId ) ;
    }
  }) ;
} ;

var purchaseTickets = function ( pRoute , pTickets , pCallBack )
{
  console.log( 'sessionId: ' + sessionId ) ;
  console.log( 'route: ' + pRoute.id ) ;
  console.log( 'tickets: ' + parseInt( pTickets ) ) ;

  $.ajax({
    type : 'POST' ,
    url : _SERVICE_URL ,
    dataType: 'json' ,
    data : {
      'sessionId' : sessionId ,
      'route' : pRoute.id ,
      'tickets' : parseInt( pTickets ) ,
      'purchase' : 'true'
    } ,
    success: function ( pData )
    {
      busRoutes = pData.busRoutes ;

      if ( sessionId != pData.sessionId )
      {
        console.log( 'old: ' + sessionId ) ;
        console.log( 'new: ' + pData.sessionId ) ;
        sessionId = pData.sessionId ;
      }

      pCallBack( busRoutes ) ;
      hideLoadingWidget() ;
    }
  });
} ;


var showLoadingWidget = function ( )
{
  if ( ! loadingWidgetOn )
  {
    $( 'body' ).addClass( 'ui-disabled' ) ;
    $.mobile.loading(
      'show' , {
        text : 'Loading' ,
        textVisible : true ,
        textonly : false ,
        theme : 'b'
      }
    ) ;
    loadingWidgetOn = true ;
  }
} ;

var hideLoadingWidget = function ( )
{
  if ( loadingWidgetOn )
  {
    $( 'body' ).removeClass( 'ui-disabled' ) ;
    $.mobile.loading( 'hide' ) ;
    loadingWidgetOn = false ;
  }
} ;


$( document ).one( 'pageinit' , main ) ;
} )() ;