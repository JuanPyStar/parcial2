<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseControlador.php';
require_once __DIR__ . '/../modelos/AdministradorModel.php';

final class AdministradorControlador extends BaseControlador
{
    private AdministradorModel $model;

    public function __construct(?AdministradorModel $model = null)
    {
        $this->model = $model ?? new AdministradorModel();
    }

    public function crear(array $input): array
    {
        try {
            $id = $this->model->crear(
                (string)($input['nombre'] ?? ''),
                (string)($input['correo'] ?? ''),
                (string)($input['rol'] ?? '')
            );
            return $this->ok(['id_admin' => $id]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo crear administrador.', $e->getMessage());
        }
    }

    public function buscar(int $id_admin): array
    {
        try {
            return $this->ok($this->model->buscarPorId($id_admin));
        } catch (Throwable $e) {
            return $this->fail('No se pudo buscar administrador.', $e->getMessage());
        }
    }

    public function listar(): array
    {
        try {
            return $this->ok($this->model->listar());
        } catch (Throwable $e) {
            return $this->fail('No se pudo listar administradores.', $e->getMessage());
        }
    }

    public function editar(int $id_admin, array $input): array
    {
        try {
            $ok = $this->model->editar(
                $id_admin,
                (string)($input['nombre'] ?? ''),
                (string)($input['correo'] ?? ''),
                (string)($input['rol'] ?? '')
            );
            return $this->ok(['updated' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo editar administrador.', $e->getMessage());
        }
    }

    public function eliminar(int $id_admin): array
    {
        try {
            $ok = $this->model->eliminar($id_admin);
            return $this->ok(['deleted' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo eliminar administrador.', $e->getMessage());
        }
    }
}

