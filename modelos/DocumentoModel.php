<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class DocumentoModel extends BaseModel
{
    public function crear(string $nombre_archivo, string $ruta, string $tipo, int $id_solicitud): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO documento (nombre_archivo, ruta, tipo, id_solicitud)
             VALUES (:nombre_archivo, :ruta, :tipo, :id_solicitud)'
        );
        $stmt->execute([
            ':nombre_archivo' => $nombre_archivo,
            ':ruta' => $ruta,
            ':tipo' => $tipo,
            ':id_solicitud' => $id_solicitud,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id_documento): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM documento WHERE id_documento = :id');
        $stmt->execute([':id' => $id_documento]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function listar(): array
    {
        return $this->pdo->query('SELECT * FROM documento ORDER BY id_documento DESC')->fetchAll();
    }

    public function listarPorSolicitud(int $id_solicitud): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM documento WHERE id_solicitud = :id ORDER BY id_documento DESC');
        $stmt->execute([':id' => $id_solicitud]);
        return $stmt->fetchAll();
    }

    public function editar(int $id_documento, string $nombre_archivo, string $ruta, string $tipo, int $id_solicitud): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE documento
             SET nombre_archivo = :nombre_archivo,
                 ruta = :ruta,
                 tipo = :tipo,
                 id_solicitud = :id_solicitud
             WHERE id_documento = :id'
        );
        $stmt->execute([
            ':nombre_archivo' => $nombre_archivo,
            ':ruta' => $ruta,
            ':tipo' => $tipo,
            ':id_solicitud' => $id_solicitud,
            ':id' => $id_documento,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(int $id_documento): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM documento WHERE id_documento = :id');
        $stmt->execute([':id' => $id_documento]);
        return $stmt->rowCount() > 0;
    }
}

