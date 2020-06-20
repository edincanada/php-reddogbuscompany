DROP TABLE IF EXISTS _tblRdbcBusRoutes ;
CREATE TABLE _tblRdbcBusRoutes (

  _id          VARCHAR( 128 )    NOT NULL DEFAULT '' UNIQUE PRIMARY KEY,
  _origin      VARCHAR( 128 )    NOT NULL DEFAULT '' ,
  _destination VARCHAR( 128 )    NOT NULL DEFAULT '' ,
  _seats       SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  _ticketPrice DECIMAL( 10 , 2 ) NOT NULL DEFAULT 0.00
) ;

DROP TABLE IF EXISTS _tblRdbcSessions ;
CREATE TABLE _tblRdbcSessions (

  _sessionId BINARY( 16 )      NOT NULL ,
  _id        VARCHAR( 128 )    NOT NULL DEFAULT '' ,
  _seats     SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  _time      DATETIME          NOT NULL ,

  CONSTRAINT SessionsPk PRIMARY KEY( _id , _sessionId ) ,

  CONSTRAINT SessionsFk_routeId
    FOREIGN KEY( _id )
	  REFERENCES _tblRdbcBusRoutes( _id )

) ;

DROP VIEW IF EXISTS viewRdbcRouteSessions ;
CREATE VIEW viewRdbcRouteSessions AS
SELECT
  HEX( ss._sessionId ) AS 'sessionId' ,
  ss._id AS 'routeId' ,
  rr._origin AS 'origin' ,
  rr._destination AS 'destination' ,
  rr._seats AS 'seats' ,
  ss._seats AS 'seatsAvailable' ,
  rr._ticketPrice AS 'ticketPrice'
FROM _tblRdbcBusRoutes rr , _tblRdbcSessions ss
WHERE
  ss._id = rr._id AND
  ss._time >= ( NOW() - INTERVAL 5 MINUTE ) ;