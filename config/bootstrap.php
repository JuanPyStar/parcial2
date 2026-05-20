<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

/**
 * Asegura columnas mínimas para autenticación.
 * (Evita que el registro/login falle si no ejecutaron la migración.)
 */
function ensureAuthSchema(): void
{
    $pdo = Database::pdo();
    $db = $pdo->query('SELECT DATABASE() d')->fetch()['d'] ?? null;
    if (!$db) {
        return;
    }

    $st = $pdo->prepare(
        'SELECT COUNT(*) c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );

    $needs = [
        // El proyecto usa columna `contraseña`. Si no existe, la creamos.
        ['estudiante', 'contraseña', 'ALTER TABLE estudiante ADD COLUMN `contraseña` VARCHAR(255) NULL AFTER semestre'],
        ['administrador', 'contraseña', 'ALTER TABLE administrador ADD COLUMN `contraseña` VARCHAR(255) NULL AFTER rol'],
    ];

    foreach ($needs as [$table, $col, $ddl]) {
        $st->execute([$db, $table, $col]);
        $exists = (int)($st->fetch()['c'] ?? 0) > 0;
        if (!$exists) {
            $pdo->exec($ddl);
        }
    }
}

/**
 * Asegura tabla/columnas mínimas para solicitudes académicas.
 */
function ensureSolicitudSchema(): void
{
    $pdo = Database::pdo();
    $db = $pdo->query('SELECT DATABASE() d')->fetch()['d'] ?? null;
    if (!$db) {
        return;
    }

    $st = $pdo->prepare('SELECT COUNT(*) c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?');
    $st->execute([$db, 'solicitud']);
    $existsTable = (int)($st->fetch()['c'] ?? 0) > 0;

    if (!$existsTable) {
        $pdo->exec(
            'CREATE TABLE solicitud (
                id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
                fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
                estado VARCHAR(30) NOT NULL DEFAULT \'Pendiente\',
                descripcion TEXT NULL,
                id_estudiante INT NOT NULL,
                id_tipo_solicitud INT NOT NULL,
                programa_id INT NULL,
                sede_id INT NULL,
                jornada_id INT NULL,
                documento VARCHAR(255) NULL,
                observacion TEXT NULL,
                admin_id INT NULL,
                respuesta_fecha DATE NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        return;
    }

    $stCol = $pdo->prepare(
        'SELECT COUNT(*) c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );

    $needs = [
        ['solicitud', 'programa_id', 'ALTER TABLE solicitud ADD COLUMN programa_id INT NULL AFTER id_tipo_solicitud'],
        ['solicitud', 'sede_id', 'ALTER TABLE solicitud ADD COLUMN sede_id INT NULL AFTER programa_id'],
        ['solicitud', 'jornada_id', 'ALTER TABLE solicitud ADD COLUMN jornada_id INT NULL AFTER sede_id'],
        ['solicitud', 'documento', 'ALTER TABLE solicitud ADD COLUMN documento VARCHAR(255) NULL AFTER jornada_id'],
        ['solicitud', 'observacion', 'ALTER TABLE solicitud ADD COLUMN observacion TEXT NULL AFTER documento'],
        ['solicitud', 'admin_id', 'ALTER TABLE solicitud ADD COLUMN admin_id INT NULL AFTER observacion'],
        ['solicitud', 'respuesta_fecha', 'ALTER TABLE solicitud ADD COLUMN respuesta_fecha DATE NULL AFTER admin_id'],
    ];

    foreach ($needs as [$table, $col, $ddl]) {
        $stCol->execute([$db, $table, $col]);
        $exists = (int)($stCol->fetch()['c'] ?? 0) > 0;
        if (!$exists) {
            $pdo->exec($ddl);
        }
    }
}

