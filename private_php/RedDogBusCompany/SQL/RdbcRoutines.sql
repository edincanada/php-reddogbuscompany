DROP FUNCTION IF EXISTS RdbcCreateSession ;
DELIMITER $$
CREATE FUNCTION RdbcCreateSession ( ) RETURNS CHAR( 32 )
NOT DETERMINISTIC
CONTAINS SQL
MODIFIES SQL DATA
BEGIN

  DECLARE retSessionId CHAR( 32 ) ;
  DECLARE uuidAsString VARCHAR( 64 ) ;
  DECLARE sessionUUID VARBINARY( 32 ) ;
  DECLARE sessionIdsThatMatch INT ;
  DECLARE timeCreated DATETIME ;

  SET sessionUUID = UNHEX( REPLACE( UUID() , '-' , '' )) ;

  SELECT COUNT( _sessionId )
  INTO sessionIdsThatMatch
  FROM _tblRdbcSessions
  WHERE _sessionId = sessionUUID ;

  WHILE ( sessionIdsThatMatch > 0 ) DO
    SET sessionUUID = UNHEX( REPLACE( UUID() , '-' , '' )) ;

    SELECT COUNT( _sessionId )
    INTO sessionIdsThatMatch
    FROM _tblRdbcSessions
    WHERE _sessionId = sessionUUID ;
  END WHILE ;

  SET timeCreated = NOW() ;
  INSERT INTO _tblRdbcSessions ( _sessionId , _id , _seats , _time )
  SELECT sessionUUID , Route._id , Route._seats, timeCreated
  FROM _tblRdbcBusRoutes Route ;

  SET retSessionId = HEX( sessionUUID ) ;

  RETURN retSessionId ;
END ;
$$
DELIMITER ;

DROP FUNCTION IF EXISTS RdbcDeleteSession ;
DELIMITER $$
CREATE FUNCTION RdbcDeleteSession (
  pSessionId CHAR( 32 )
)
RETURNS BIT
NOT DETERMINISTIC
CONTAINS SQL
MODIFIES SQL DATA
BEGIN

  DECLARE sessionFound INT ;
  DECLARE sessionIdBinary BINARY( 16 ) ;
  DECLARE retValue BIT ;

  SET sessionIdBinary = UNHEX( pSessionId ) ;

  SELECT count( _sessionId )
  INTO sessionFound
  FROM _tblRdbcSessions
  WHERE _sessionId = sessionIdBinary ;

  IF ( sessionFound > 0 ) THEN
    DELETE FROM _tblRdbcSessions WHERE _sessionId = sessionIdBinary ;
  END IF ;

  RETURN ( sessionFound > 0 ) ;
END ;
$$
DELIMITER ;

DROP FUNCTION IF EXISTS RdbcUpdateTickets ;
DELIMITER $$
CREATE FUNCTION RdbcUpdateTickets (
  pSessionId CHAR( 32 ) ,
  pRouteId   VARCHAR( 128 ) ,
  PSeats     SMALLINT UNSIGNED
)
RETURNS BIT
NOT DETERMINISTIC
CONTAINS SQL
MODIFIES SQL DATA
BEGIN

  DECLARE sessionFound INT ;
  DECLARE sessionIdBinary BINARY( 16 ) ;
  DECLARE retValue BIT ;
  DECLARE timeUpdated DATETIME ;

  SET sessionIdBinary = UNHEX( pSessionId ) ;

  SELECT count( _sessionId )
  INTO sessionFound
  FROM _tblRdbcSessions
  WHERE _sessionId = sessionIdBinary ;

  IF ( sessionFound > 0 ) THEN
    SET timeUpdated = NOW() ;

    UPDATE _tblRdbcSessions
    SET _seats = pSeats , _time = timeUpdated
    WHERE _sessionId = sessionIdBinary AND _id LIKE pRouteId ;
  END IF ;

  RETURN ( sessionFound > 0 ) ;
END ;
$$
DELIMITER ;