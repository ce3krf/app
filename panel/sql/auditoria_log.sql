-- ************************************************************
-- Tabla de auditoría
-- Registra toda la actividad de los usuarios del panel.
--
-- Cómo leerla:
--   * Filas con campo = NULL  -> el EVENTO (quién, qué acción, cuándo, sobre qué registro)
--   * Filas con campo <> NULL -> el DETALLE de ese evento (campo, valor anterior, valor nuevo)
--   * Todas las filas de una misma operación comparten operacion_id
--
-- Acciones posibles:
--   LOGIN, LOGIN_FALLIDO, LOGOUT, VER, CREAR, EDITAR, ELIMINAR,
--   CAMBIO_PASSWORD, RESPALDO
-- ************************************************************

CREATE TABLE IF NOT EXISTS auditoria_log (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    fecha_hora      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    operacion_id    CHAR(16)        NOT NULL,

    -- Quién
    usuario_id      INT             NULL,
    usuario_login   VARCHAR(100)    NULL,
    usuario_nombre  VARCHAR(150)    NULL,
    perfil          VARCHAR(50)     NULL,
    ip              VARCHAR(45)     NULL,
    user_agent      VARCHAR(255)    NULL,

    -- Qué hizo y dónde
    accion          VARCHAR(20)     NOT NULL,
    opcion          VARCHAR(100)    NULL COMMENT 'Pantalla del panel (ej. proyectos_editar.php)',
    script          VARCHAR(100)    NULL COMMENT 'Archivo que ejecutó la acción (ej. api/api_proyectos.php)',

    -- Sobre qué
    tabla           VARCHAR(64)     NULL,
    registro_id     VARCHAR(64)     NULL,
    registro_desc   VARCHAR(255)    NULL COMMENT 'Texto legible del registro (se conserva aunque se elimine)',
    proyecto_id     INT             NULL COMMENT 'Iniciativa relacionada (proyectos, actividades, indicadores)',

    -- Detalle del cambio
    campo           VARCHAR(64)     NULL,
    valor_anterior  MEDIUMTEXT      NULL,
    valor_nuevo     MEDIUMTEXT      NULL,
    detalle         VARCHAR(500)    NULL,

    PRIMARY KEY (id),
    KEY idx_fecha      (fecha_hora),
    KEY idx_usuario    (usuario_id, fecha_hora),
    KEY idx_registro   (tabla, registro_id),
    KEY idx_proyecto   (proyecto_id, fecha_hora),
    KEY idx_accion     (accion, fecha_hora),
    KEY idx_operacion  (operacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ************************************************************
-- OPCIONAL (recomendado): impedir que el log se modifique o borre.
-- Si el hosting no permite crear triggers, se puede omitir.
-- Para ejecutarlo en phpMyAdmin, cambiar el delimitador a $$.
-- ************************************************************
-- DELIMITER $$
-- CREATE TRIGGER auditoria_log_no_update BEFORE UPDATE ON auditoria_log
-- FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'auditoria_log es de solo lectura'$$
-- CREATE TRIGGER auditoria_log_no_delete BEFORE DELETE ON auditoria_log
-- FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'auditoria_log es de solo lectura'$$
-- DELIMITER ;
