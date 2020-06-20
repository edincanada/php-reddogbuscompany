( function ( )
{

var _addEventListener = function ( pElement , pListener , pEventName ) { } ;
var _removeEventListener = function ( pElement , pListener , pEventName ) { } ;

if ( window.addEventListener )
{
  _addEventListener = function ( pElement , pListener , pEventName )
  {
    pElement.addEventListener( pEventName , pListener , false ) ;
  } ;
}
else if ( window.attachEvent )
{
  _addEventListener = function ( pElement , pListener , pEventName )
  {
    pElement.attachEvent( 'on' + pEventName , pListener ) ;
  } ;
}

if ( window.removeEventListener )
{
  _removeEventListener = function ( pElement , pListener , pEventName )
  {
    pElement.removeEventListener( pEventName , pListener ) ;
  } ;
}
else if ( window.detachEvent )
{
  _removeEventListener = function ( pElement , pListener , pEventName )
  {
    pElement.detachEvent( 'on' + pEventName , pListener ) ;
  } ;
}


//Constants
var FORM_ID = 'formRedDogBusCompany' ;
var SELECT_ROUTE_ID = 'sltRouteSelection' ;
var TICKET_BUTTONS_NAME = 'rblNumberOfTicketsToPurchase' ;
var TICKET_BUTTONS_ID_PREFIX = 'rbtNumberOfTicketsToPurchase_' ;
var PURCHASE_BUTTON_ID = 'btnPurchase' ;
var SENDER_FIELD_ID = 'hdnPostBackSenderId' ;
var SLT_SEE_THE_CODE_ID = 'sltSeeTheCode' ;

var Dom = { } ;
Dom.form = null ;
Dom.sltRouteSelection = null ;
Dom.rblNumberOfTicketsToPurchase = [ ] ;
Dom.btnPurchase = null ;
Dom.hdnPostBackSenderId = null ;
Dom.sltSeeTheCode = null ;

var radioButtonEventListeners = [ ] ;
var sourceCodeWindow ;

var initializeDomReferences = function ( )
{
  Dom.form = document.getElementById( FORM_ID ) ;
  Dom.sltRouteSelection = document.getElementById( SELECT_ROUTE_ID ) ;
  Dom.btnPurchase = document.getElementById( PURCHASE_BUTTON_ID ) ;
  Dom.hdnPostBackSenderId = document.getElementById( SENDER_FIELD_ID ) ;
  Dom.sltSeeTheCode = document.getElementById( SLT_SEE_THE_CODE_ID ) ;

  var ticketButtonsCount =
    document.getElementsByName( TICKET_BUTTONS_NAME ).length ;

  var ii = 0 ;

  for ( ii = 1 ; ii <= ticketButtonsCount ; ii ++ )
  {
    Dom.rblNumberOfTicketsToPurchase.push(
      document.getElementById( TICKET_BUTTONS_ID_PREFIX + ii )
    ) ;
  }

  _addEventListener(
    Dom.sltSeeTheCode ,
    function ( )
    {
      var selectedIndex = Dom.sltSeeTheCode.selectedIndex ;

      if ( selectedIndex > 0 )
      {
        if ( sourceCodeWindow == null || sourceCodeWindow.location == null )
        {
          sourceCodeWindow =
            window.open(
            Dom.sltSeeTheCode.options[ selectedIndex ].value ,
            'SourceCode'
          ) ;
        }
        else
        {
          sourceCodeWindow.location.href =
            Dom.sltSeeTheCode.options[ selectedIndex ].value ;

          sourceCodeWindow.focus() ;
        }
      }
    } ,
    'change'
  ) ;

} ;

var doPostBack = function ( pPostBackElementId )
{
  Dom.hdnPostBackSenderId.value = pPostBackElementId ;
  Dom.form.submit() ;
} ;

var addPostBackListeners = function ( )
{
  _addEventListener( Dom.sltRouteSelection , onRouteSelected , 'change' ) ;

  var ii = 0 ;
  for( ii = 0 ; ii < Dom.rblNumberOfTicketsToPurchase.length ; ii ++ )
  {
    if ( ! Dom.rblNumberOfTicketsToPurchase[ ii ].checked )
    {
      radioButtonEventListeners.push(
        onTicketsSelectedHandler( Dom.rblNumberOfTicketsToPurchase[ ii ].value )
      ) ;

      _addEventListener(
        Dom.rblNumberOfTicketsToPurchase[ ii ] ,
        radioButtonEventListeners[ ii ] ,
        'click'
      ) ;
    }
    else
    {
      radioButtonEventListeners.push( null ) ;
    }
  }


  if ( ! Dom.btnPurchase.disabled )
  {
    _addEventListener(
      Dom.btnPurchase ,
      onPurchaseConfirmed ,
      'click'
    ) ;
  }
} ;

var removePostbackListeners = function ( )
{
  _removeEventListener( Dom.sltRouteSelection , onRouteSelected , 'change' ) ;

  var ii = 0 ;
  for( ii = 0 ; ii < Dom.rblNumberOfTicketsToPurchase.length ; ii ++ )
  {
    if ( Dom.rblNumberOfTicketsToPurchase[ ii ] != null )
    {
      _removeEventListener(
        Dom.rblNumberOfTicketsToPurchase[ ii ] ,
        radioButtonEventListeners[ ii ] ,
        'click'
      ) ;
    }
  }

  if ( ! Dom.btnPurchase.disabled )
  {
    _removeEventListener(
      Dom.btnPurchase ,
      onPurchaseConfirmed ,
      'click'
    ) ;
  }
}

var onRouteSelected = function ( )
{
  removePostbackListeners() ;
  doPostBack( SELECT_ROUTE_ID ) ;
} ;

var onTicketsSelectedHandler = function ( pValue )
{
  return function ( )
  {
    removePostbackListeners() ;
    doPostBack( TICKET_BUTTONS_NAME ) ;
  } ;
} ;

var onPurchaseConfirmed = function ( )
{
  removePostbackListeners() ;
  Dom.hdnPostBackSenderId.value = PURCHASE_BUTTON_ID ;
} ;

var onDocumentLoaded = function ( )
{
  initializeDomReferences() ;
  addPostBackListeners() ;
} ;

_addEventListener(
 window ,
 onDocumentLoaded ,
 'load'
) ;

} )() ;