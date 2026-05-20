<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseControlador.php';
require_once __DIR__ . '/../modelos/TipoSolicitudModel.php';

final class TipoSolicitudControlador extends BaseControlador
{
    private TipoSolicitudModel $model;

    public function __construct(?TipoSolicitudModel $model = null)
    {
        $this->model = $model ?? new TipoSolicitudModel();
    }

    public function crear(array $input): array
    {
        try {
            $id = $this->model->crear((string)($input['nombre_tipo'] ?? ''));
            return $this->ok(['id_tipo_solicitud' => $id]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo crear tipo de solicitud.', $e->getMessage());
        }
    }

    public function buscar(int $id_tipo_solicitud): array
    {
        try {
            return $this->ok($this->model->buscarPorId($id_tipo_solicitud));
        } catch (Throwable $e) {
            return $this->fail('No se pudo buscar tipo de solicitud.', $e->getMessage());
        }
    }

    public function listar(): array
    {
        try {
            return $this->ok($this->model->listar());
        } catch (Throwable $e) {
            return $this->fail('No se pudo listar tipos de solicitud.', $e->getMessage());
        }
    }

    public function editar(int $id_tipo_solicitud, array $input): array
    {
        try {
            $ok = $this->model->editar($id_tipo_solicitud, (string)($input['nombre_tipo'] ?? ''));
            return $this->ok(['updated' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo editar tipo de solicitud.', $e->getMessage());
        }
    }

    public function eliminar(int $id_tipo_solicitud): array
    {
        try {
            $ok = $this->model->eliminar($id_tipo_solicitud);
            return $this->ok(['deleted' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo eliminar tipo de solicitud.', $e->getMessage());
        }
    }
}

