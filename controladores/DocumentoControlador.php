<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseControlador.php';
require_once __DIR__ . '/../modelos/DocumentoModel.php';

final class DocumentoControlador extends BaseControlador
{
    private DocumentoModel $model;

    public function __construct(?DocumentoModel $model = null)
    {
        $this->model = $model ?? new DocumentoModel();
    }

    public function crear(array $input): array
    {
        try {
            $id = $this->model->crear(
                (string)($input['nombre_archivo'] ?? ''),
                (string)($input['ruta'] ?? ''),
                (string)($input['tipo'] ?? ''),
                (int)($input['id_solicitud'] ?? 0)
            );
            return $this->ok(['id_documento' => $id]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo crear documento.', $e->getMessage());
        }
    }

    public function buscar(int $id_documento): array
    {
        try {
            return $this->ok($this->model->buscarPorId($id_documento));
        } catch (Throwable $e) {
            return $this->fail('No se pudo buscar documento.', $e->getMessage());
        }
    }

    public function listar(): array
    {
        try {
            return $this->ok($this->model->listar());
        } catch (Throwable $e) {
            return $this->fail('No se pudo listar documentos.', $e->getMessage());
        }
    }

    public function listarPorSolicitud(int $id_solicitud): array
    {
        try {
            return $this->ok($this->model->listarPorSolicitud($id_solicitud));
        } catch (Throwable $e) {
            return $this->fail('No se pudo listar documentos por solicitud.', $e->getMessage());
        }
    }

    public function editar(int $id_documento, array $input): array
    {
        try {
            $ok = $this->model->editar(
                $id_documento,
                (string)($input['nombre_archivo'] ?? ''),
                (string)($input['ruta'] ?? ''),
                (string)($input['tipo'] ?? ''),
                (int)($input['id_solicitud'] ?? 0)
            );
            return $this->ok(['updated' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo editar documento.', $e->getMessage());
        }
    }

    public function eliminar(int $id_documento): array
    {
        try {
            $ok = $this->model->eliminar($id_documento);
            return $this->ok(['deleted' => $ok]);
        } catch (Throwable $e) {
            return $this->fail('No se pudo eliminar documento.', $e->getMessage());
        }
    }
}

