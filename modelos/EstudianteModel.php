<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class EstudianteModel extends BaseModel
{
    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM estudiante WHERE correo = :correo LIMIT 1');
        $stmt->execute([':correo' => $correo]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function crear(
        string $nombre,
        string $apellido,
        string $documento,
        string $correo,
        ?string $telefono,
        string $programa,
        int $semestre
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO estudiante (nombre, apellido, documento, correo, telefono, programa, semestre)
             VALUES (:nombre, :apellido, :documento, :correo, :telefono, :programa, :semestre)'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':documento' => $documento,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':programa' => $programa,
            ':semestre' => $semestre,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function crearConPassword(
        string $nombre,
        string $apellido,
        string $documento,
        string $correo,
        ?string $telefono,
        string $programa,
        int $semestre,
        string $password_hash
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO estudiante (nombre, apellido, documento, correo, telefono, programa, semestre, `contraseña`)
             VALUES (:nombre, :apellido, :documento, :correo, :telefono, :programa, :semestre, :contrasena)'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':documento' => $documento,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':programa' => $programa,
            ':semestre' => $semestre,
            ':contrasena' => $password_hash,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id_estudiante): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM estudiante WHERE id_estudiante = :id');
        $stmt->execute([':id' => $id_estudiante]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function listar(): array
    {
        return $this->pdo->query('SELECT * FROM estudiante ORDER BY id_estudiante DESC')->fetchAll();
    }

    public function editar(
        int $id_estudiante,
        string $nombre,
        string $apellido,
        string $documento,
        string $correo,
        ?string $telefono,
        string $programa,
        int $semestre
    ): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE estudiante
             SET nombre = :nombre,
                 apellido = :apellido,
                 documento = :documento,
                 correo = :correo,
                 telefono = :telefono,
                 programa = :programa,
                 semestre = :semestre
             WHERE id_estudiante = :id'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':documento' => $documento,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':programa' => $programa,
            ':semestre' => $semestre,
            ':id' => $id_estudiante,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function actualizarPassword(int $id_estudiante, string $password_hash): bool
    {
        $stmt = $this->pdo->prepare('UPDATE estudiante SET `contraseña` = :hash WHERE id_estudiante = :id');
        $stmt->execute([':hash' => $password_hash, ':id' => $id_estudiante]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(int $id_estudiante): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM estudiante WHERE id_estudiante = :id');
        $stmt->execute([':id' => $id_estudiante]);
        return $stmt->rowCount() > 0;
    }
}

