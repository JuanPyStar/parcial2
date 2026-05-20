<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseControlador.php';
require_once __DIR__ . '/../modelos/EstudianteModel.php';

final class EstudianteControlador extends BaseControlador
{
    private EstudianteModel $model;

    public function __construct(?EstudianteModel $model = null)
    {
        $this->model = $model ?? new EstudianteModel();
    }

    public function crear(array $input): array
    {
        try {
            $id = $this->model->crear(
                (string)($input['nombre'] ?? ''),
                (string)($input['apellido'] ?? ''),
                (string)($input['documento'] ?? ''),
                (string)($input['correo'] ?? ''),
                $input['telefono'] ?? null,
                (string)($input['programa'] ?? ''),
                (int)($input['semestre'] ?? 0)
            );
            return $this->ok(['id_estudiante' => $id]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo crear estudiante.', $e->getMessage());
        }
    }

    public function buscar(int $id_estudiante): array
    {
        try {
            return $this->ok($this->model->buscarPorId($id_estudiante));
        } catch (Throwable $e) {
            return $this->fail('No se pudo buscar estudiante.', $e->getMessage());
        }
    }

    public function listar(): array
    {
        try {
            return $this->ok($this->model->listar());
        } catch (Throwable $e) {
            return $this->fail('No se pudo listar estudiantes.', $e->getMessage());
        }
    }

    public function editar(int $id_estudiante, array $input): array
    {
        try {
            $ok = $this->model->editar(
                $id_estudiante,
                (string)($input['nombre'] ?? ''),
                (string)($input['apellido'] ?? ''),
                (string)($input['documento'] ?? ''),
                (string)($input['correo'] ?? ''),
                $input['telefono'] ?? null,
                (string)($input['programa'] ?? ''),
                (int)($input['semestre'] ?? 0)
            );
            return $this->ok(['updated' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo editar estudiante.', $e->getMessage());
        }
    }

    public function eliminar(int $id_estudiante): array
    {
        try {
            $ok = $this->model->eliminar($id_estudiante);
            return $this->ok(['deleted' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo eliminar estudiante.', $e->getMessage());
        }
    }
}

