<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseControlador.php';
require_once __DIR__ . '/../modelos/RespuestaSolicitudModel.php';

final class RespuestaSolicitudControlador extends BaseControlador
{
    private RespuestaSolicitudModel $model;

    public function __construct(?RespuestaSolicitudModel $model = null)
    {
        $this->model = $model ?? new RespuestaSolicitudModel();
    }

    public function crear(array $input): array
    {
        try {
            $id = $this->model->crear(
                $input['fecha_respuesta'] ?? null,
                $input['observacion'] ?? null,
                (string)($input['estado_final'] ?? ''),
                $input['archivo_respuesta'] ?? null,
                $input['ruta_archivo_respuesta'] ?? null,
                (int)($input['id_solicitud'] ?? 0),
                (int)($input['id_admin'] ?? 0)
            );
            return $this->ok(['id_respuesta' => $id]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo crear respuesta.', $e->getMessage());
        }
    }

    public function buscar(int $id_respuesta): array
    {
        try {
            return $this->ok($this->model->buscarPorId($id_respuesta));
        } catch (Throwable $e) {
            return $this->fail('No se pudo buscar respuesta.', $e->getMessage());
        }
    }

    public function buscarPorSolicitud(int $id_solicitud): array
    {
        try {
            return $this->ok($this->model->buscarPorSolicitud($id_solicitud));
        } catch (Throwable $e) {
            return $this->fail('No se pudo buscar respuesta por solicitud.', $e->getMessage());
        }
    }

    public function listar(): array
    {
        try {
            return $this->ok($this->model->listar());
        } catch (Throwable $e) {
            return $this->fail('No se pudo listar respuestas.', $e->getMessage());
        }
    }

    public function editar(int $id_respuesta, array $input): array
    {
        try {
            $ok = $this->model->editar(
                $id_respuesta,
                $input['fecha_respuesta'] ?? null,
                $input['observacion'] ?? null,
                (string)($input['estado_final'] ?? ''),
                $input['archivo_respuesta'] ?? null,
                $input['ruta_archivo_respuesta'] ?? null,
                (int)($input['id_solicitud'] ?? 0),
                (int)($input['id_admin'] ?? 0)
            );
            return $this->ok(['updated' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo editar respuesta.', $e->getMessage());
        }
    }

    public function eliminar(int $id_respuesta): array
    {
        try {
            $ok = $this->model->eliminar($id_respuesta);
            return $this->ok(['deleted' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo eliminar respuesta.', $e->getMessage());
        }
    }
}

