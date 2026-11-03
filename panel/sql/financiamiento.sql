--Table: financiamiento

--DROP TABLE IF EXISTS financiamiento;

CREATE TABLE financiamiento (
  id      int AUTO_INCREMENT NOT NULL,
  numero  int DEFAULT NULL,
  etapa   int DEFAULT NULL,
  monto   int DEFAULT NULL,
  fuente  varchar(255) DEFAULT NULL,
  /* Keys */
  PRIMARY KEY (id)
) ENGINE = InnoDB;