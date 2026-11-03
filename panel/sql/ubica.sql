CREATE TABLE ubicaciones (
  id           int AUTO_INCREMENT NOT NULL PRIMARY KEY,
  numero       int,
  descripcion  varchar(100),
  latitud      varchar(100),
  longitud     varchar(100)
) ENGINE = InnoDB;