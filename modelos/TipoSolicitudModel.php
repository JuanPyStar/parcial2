<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class TipoSolicitudModel extends BaseModel
{
    public function crear(string $nombre_tipo): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO tipo_solicitud (nombre_tipo) VALUES (:nombre)');
        $stmt->execute([':nombre' => $nombre_tipo]);
        return (int)$this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id_tipo_solicitud): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tipo_solicitud WHERE id_tipo_solicitud = :id');
        $stmt->execute([':id' => $id_tipo_solicitud]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function listar(): array
    {
        return $this->pdo->query('SELECT * FROM tipo_solicitud ORDER BY id_tipo_solicitud DESC')->fetchAll();
    }

    public function editar(int $id_tipo_solicitud, string $nombre_tipo): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tipo_solicitud SET nombre_tipo = :nombre WHERE id_tipo_solicitud = :id'
        );
        $stmt->execute([
            ':nombre' => $nombre_tipo,
            ':id' => $id_tipo_solicitud,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(int $id_tipo_solicitud): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tipo_solicitud WHERE id_tipo_solicitud = :id');
        $stmt->execute([':id' => $id_tipo_solicitud]);
        return $stmt->rowCount() > 0;
    }
}

