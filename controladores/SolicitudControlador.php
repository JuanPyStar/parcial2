<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseControlador.php';
require_once __DIR__ . '/../modelos/SolicitudModel.php';

final class SolicitudControlador extends BaseControlador
{
    private SolicitudModel $model;

    public function __construct(?SolicitudModel $model = null)
    {
        $this->model = $model ?? new SolicitudModel();
    }

    public function crear(array $input): array
    {
        try {
            $id = $this->model->crear(
                $input['fecha'] ?? null,
                $input['estado'] ?? null,
                $input['descripcion'] ?? null,
                (int)($input['id_estudiante'] ?? 0),
                (int)($input['id_tipo_solicitud'] ?? 0)
            );
            return $this->ok(['id_solicitud' => $id]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo crear solicitud.', $e->getMessage());
        }
    }

    public function buscar(int $id_solicitud): array
    {
        try {
            return $this->ok($this->model->buscarPorId($id_solicitud));
        } catch (Throwable $e) {
            return $this->fail('No se pudo buscar solicitud.', $e->getMessage());
        }
    }

    public function listar(): array
    {
        try {
            return $this->ok($this->model->listar());
        } catch (Throwable $e) {
            return $this->fail('No se pudo listar solicitudes.', $e->getMessage());
        }
    }

    public function editar(int $id_solicitud, array $input): array
    {
        try {
            $ok = $this->model->editar(
                $id_solicitud,
                $input['fecha'] ?? null,
                (string)($input['estado'] ?? ''),
                $input['descripcion'] ?? null,
                (int)($input['id_estudiante'] ?? 0),
                (int)($input['id_tipo_solicitud'] ?? 0)
            );
            return $this->ok(['updated' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo editar solicitud.', $e->getMessage());
        }
    }

    public function eliminar(int $id_solicitud): array
    {
        try {
            $ok = $this->model->eliminar($id_solicitud);
            return $this->ok(['deleted' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo eliminar solicitud.', $e->getMessage());
        }
    }
}

