<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class AdministradorModel extends BaseModel
{
    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM administrador WHERE correo = :correo LIMIT 1');
        $stmt->execute([':correo' => $correo]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function crear(string $nombre, string $correo, string $rol): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO administrador (nombre, correo, rol) VALUES (:nombre, :correo, :rol)'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':rol' => $rol,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function crearConPassword(string $nombre, string $correo, string $rol, string $password_hash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO administrador (nombre, correo, rol, `contraseña`)
             VALUES (:nombre, :correo, :rol, :hash)'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':rol' => $rol,
            ':hash' => $password_hash,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id_admin): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM administrador WHERE id_admin = :id');
        $stmt->execute([':id' => $id_admin]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function listar(): array
    {
        return $this->pdo->query('SELECT * FROM administrador ORDER BY id_admin DESC')->fetchAll();
    }

    public function editar(int $id_admin, string $nombre, string $correo, string $rol): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE administrador SET nombre = :nombre, correo = :correo, rol = :rol WHERE id_admin = :id'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':rol' => $rol,
            ':id' => $id_admin,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(int $id_admin): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM administrador WHERE id_admin = :id');
        $stmt->execute([':id' => $id_admin]);
        return $stmt->rowCount() > 0;
    }

    public function actualizarPassword(int $id_admin, string $password_hash): bool
    {
        $stmt = $this->pdo->prepare('UPDATE administrador SET `contraseña` = :hash WHERE id_admin = :id');
        $stmt->execute([':hash' => $password_hash, ':id' => $id_admin]);
        return $stmt->rowCount() > 0;
    }
}

