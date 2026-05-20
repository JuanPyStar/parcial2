<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class RespuestaSolicitudModel extends BaseModel
{
    public function crear(
        ?string $fecha_respuesta,
        ?string $observacion,
        string $estado_final,
        ?string $archivo_respuesta,
        ?string $ruta_archivo_respuesta,
        int $id_solicitud,
        int $id_admin
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO respuesta_solicitud (
                fecha_respuesta,
                observacion,
                estado_final,
                archivo_respuesta,
                ruta_archivo_respuesta,
                id_solicitud,
                id_admin
             ) VALUES (
                COALESCE(:fecha_respuesta, CURDATE()),
                :observacion,
                :estado_final,
                :archivo_respuesta,
                :ruta_archivo_respuesta,
                :id_solicitud,
                :id_admin
             )'
        );
        $stmt->execute([
            ':fecha_respuesta' => $fecha_respuesta,
            ':observacion' => $observacion,
            ':estado_final' => $estado_final,
            ':archivo_respuesta' => $archivo_respuesta,
            ':ruta_archivo_respuesta' => $ruta_archivo_respuesta,
            ':id_solicitud' => $id_solicitud,
            ':id_admin' => $id_admin,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id_respuesta): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM respuesta_solicitud WHERE id_respuesta = :id');
        $stmt->execute([':id' => $id_respuesta]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function buscarPorSolicitud(int $id_solicitud): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM respuesta_solicitud WHERE id_solicitud = :id');
        $stmt->execute([':id' => $id_solicitud]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function listar(): array
    {
        return $this->pdo->query('SELECT * FROM respuesta_solicitud ORDER BY id_respuesta DESC')->fetchAll();
    }

    public function editar(
        int $id_respuesta,
        ?string $fecha_respuesta,
        ?string $observacion,
        string $estado_final,
        ?string $archivo_respuesta,
        ?string $ruta_archivo_respuesta,
        int $id_solicitud,
        int $id_admin
    ): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE respuesta_solicitud
             SET fecha_respuesta = COALESCE(:fecha_respuesta, fecha_respuesta),
                 observacion = :observacion,
                 estado_final = :estado_final,
                 archivo_respuesta = :archivo_respuesta,
                 ruta_archivo_respuesta = :ruta_archivo_respuesta,
                 id_solicitud = :id_solicitud,
                 id_admin = :id_admin
             WHERE id_respuesta = :id'
        );
        $stmt->execute([
            ':fecha_respuesta' => $fecha_respuesta,
            ':observacion' => $observacion,
            ':estado_final' => $estado_final,
            ':archivo_respuesta' => $archivo_respuesta,
            ':ruta_archivo_respuesta' => $ruta_archivo_respuesta,
            ':id_solicitud' => $id_solicitud,
            ':id_admin' => $id_admin,
            ':id' => $id_respuesta,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(int $id_respuesta): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM respuesta_solicitud WHERE id_respuesta = :id');
        $stmt->execute([':id' => $id_respuesta]);
        return $stmt->rowCount() > 0;
    }
}

