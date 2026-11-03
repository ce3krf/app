-- no aplicado
ALTER TABLE proyectos
  ADD gasto_total numeric(18,2);

ALTER TABLE proyectos
  MODIFY COLUMN gasto_total decimal(18,2) DEFAULT NULL
  AFTER gasto_ejecucion;

CREATE TABLE gasto (
  id      int AUTO_INCREMENT NOT NULL,
  numero  int DEFAULT NULL,
  etapa   int DEFAULT NULL,
  monto   int DEFAULT NULL,
  fuente  varchar(255) DEFAULT NULL,
  /* Keys */
  PRIMARY KEY (id)
) ENGINE = InnoDB;    