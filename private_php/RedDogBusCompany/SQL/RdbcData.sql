DELETE FROM _tblRdbcBusRoutes WHERE _id IS NOT NULL ;

INSERT INTO _tblRdbcBusRoutes (
  _id ,
  _origin ,
  _destination ,
  _seats ,
  _ticketPrice
)
VALUES (
  '01' ,
  'Toronto' ,
  'Montreal' ,
  60 ,
  40.00
) ;

INSERT INTO _tblRdbcBusRoutes (
  _id ,
  _origin ,
  _destination ,
  _seats ,
  _ticketPrice
)
VALUES (
  '02' ,
  'Toronto' ,
  'Ottawa' ,
  60 ,
  17.25
) ;

INSERT INTO _tblRdbcBusRoutes (
  _id ,
  _origin ,
  _destination ,
  _seats ,
  _ticketPrice
)
VALUES (
  '03' ,
  'Toronto' ,
  'Niagara' ,
  60 ,
  11.50
) ;

INSERT INTO _tblRdbcBusRoutes (
  _id ,
  _origin ,
  _destination ,
  _seats ,
  _ticketPrice
)
VALUES (
  '04' ,
  'Toronto' ,
  'Thunder Bay' ,
  60 ,
  14.10
) ;