CREATE TABLE acciones (
  id                 int AUTO_INCREMENT NOT NULL,
  codigo_iniciativa  varchar(100) DEFAULT NULL,
  nombre_accion      varchar(255) DEFAULT NULL,
  inicio             date DEFAULT NULL,
  fin                date DEFAULT NULL,
  verificador        varchar(255) DEFAULT NULL,
  orden              int DEFAULT NULL,
  /* Keys */
  PRIMARY KEY (id)
) ENGINE = InnoDB;