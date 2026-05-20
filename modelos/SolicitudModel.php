<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class SolicitudModel extends BaseModel
{
    public function crear(?string $fecha, ?string $estado, ?string $descripcion, int $id_estudiante, int $id_tipo_solicitud): int
    {
        return $this->crearCompleta(
            $fecha,
            $estado,
            $descripcion,
            $id_estudiante,
            $id_tipo_solicitud,
            null,
            null,
            null,
            null
        );
    }

    public function crearCompleta(
        ?string $fecha,
        ?string $estado,
        ?string $descripcion,
        int $id_estudiante,
        int $id_tipo_solicitud,
        ?int $programa_id,
        ?int $sede_id,
        ?int $jornada_id,
        ?string $documento
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO solicitud (fecha, estado, descripcion, id_estudiante, id_tipo_solicitud, programa_id, sede_id, jornada_id, documento, observacion, admin_id, respuesta_fecha)
             VALUES (COALESCE(:fecha, CURDATE()), COALESCE(:estado, \'Pendiente\'), :descripcion, :id_estudiante, :id_tipo, :programa_id, :sede_id, :jornada_id, :documento, \'\', NULL, NULL)'
        );
        $stmt->execute([
            ':fecha' => $fecha,
            ':estado' => $estado,
            ':descripcion' => $descripcion,
            ':id_estudiante' => $id_estudiante,
            ':id_tipo' => $id_tipo_solicitud,
            ':programa_id' => $programa_id,
            ':sede_id' => $sede_id,
            ':jornada_id' => $jornada_id,
            ':documento' => $documento,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id_solicitud): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM solicitud WHERE id_solicitud = :id');
        $stmt->execute([':id' => $id_solicitud]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function listar(): array
    {
        return $this->pdo->query('SELECT * FROM solicitud ORDER BY id_solicitud DESC')->fetchAll();
    }

    public function listarPorEstudiante(int $id_estudiante): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM solicitud WHERE id_estudiante = :id ORDER BY id_solicitud DESC');
        $stmt->execute([':id' => $id_estudiante]);
        return $stmt->fetchAll();
    }

    public function listarPendientes(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM solicitud WHERE estado IN (:pendiente, :falta_info, :en_espera) ORDER BY id_solicitud DESC');
        $stmt->execute([
            ':pendiente' => 'Pendiente',
            ':falta_info' => 'Falta información',
            ':en_espera' => 'En espera'
        ]);
        return $stmt->fetchAll();
    }

    public function listarRespondidas(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM solicitud WHERE estado NOT IN (:pendiente, :falta_info, :en_espera) ORDER BY id_solicitud DESC');
        $stmt->execute([
            ':pendiente' => 'Pendiente',
            ':falta_info' => 'Falta información',
            ':en_espera' => 'En espera'
        ]);
        return $stmt->fetchAll();
    }

    public function responder(int $id_solicitud, string $estado, string $observacion, int $admin_id): bool
    {
        $existing = $this->buscarPorId($id_solicitud);
        $finalObservacion = trim((string)($existing['observacion'] ?? ''));
        if ($finalObservacion !== '') {
            $finalObservacion .= "\n\nRespuesta administrador:\n" . $observacion;
        } else {
            $finalObservacion = $observacion;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE solicitud
             SET estado = :estado,
                 observacion = :observacion,
                 admin_id = :admin_id,
                 respuesta_fecha = CURDATE()
             WHERE id_solicitud = :id'
        );
        $stmt->execute([
            ':estado' => $estado,
            ':observacion' => $finalObservacion,
            ':admin_id' => $admin_id,
            ':id' => $id_solicitud,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function enviarRespuestaEstudiante(int $id_solicitud, string $respuesta): bool
    {
        $existing = $this->buscarPorId($id_solicitud);
        if (!$existing) {
            return false;
        }

        $observacion = trim((string)($existing['observacion'] ?? ''));
        if ($observacion !== '') {
            $observacion .= "\n\nRespuesta estudiante:\n" . $respuesta;
        } else {
            $observacion = "Respuesta estudiante:\n" . $respuesta;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE solicitud
             SET observacion = :observacion,
                 estado = :estado
             WHERE id_solicitud = :id'
        );
        $stmt->execute([
            ':observacion' => $observacion,
            ':estado' => 'En espera',
            ':id' => $id_solicitud,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function editar(
        int $id_solicitud,
        ?string $fecha,
        string $estado,
        ?string $descripcion,
        int $id_estudiante,
        int $id_tipo_solicitud
    ): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE solicitud
             SET fecha = COALESCE(:fecha, fecha),
                 estado = :estado,
                 descripcion = :descripcion,
                 id_estudiante = :id_estudiante,
                 id_tipo_solicitud = :id_tipo
             WHERE id_solicitud = :id'
        );
        $stmt->execute([
            ':fecha' => $fecha,
            ':estado' => $estado,
            ':descripcion' => $descripcion,
            ':id_estudiante' => $id_estudiante,
            ':id_tipo' => $id_tipo_solicitud,
            ':id' => $id_solicitud,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(int $id_solicitud): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM solicitud WHERE id_solicitud = :id');
        $stmt->execute([':id' => $id_solicitud]);
        return $stmt->rowCount() > 0;
    }
}

