-- Agrega columna de contraseña (hash) para login real.
-- Ejecuta este script DESPUÉS de importar solicitudes_academicas.sql

ALTER TABLE estudiante
  ADD COLUMN password_hash VARCHAR(255) NULL AFTER semestre;

ALTER TABLE administrador
  ADD COLUMN password_hash VARCHAR(255) NULL AFTER rol;

-- Recomendación: luego actualiza los usuarios existentes asignando un hash:
-- UPDATE estudiante SET password_hash = '$2y$...' WHERE id_estudiante = 1;
-- UPDATE administrador SET password_hash = '$2y$...' WHERE id_admin = 1;

