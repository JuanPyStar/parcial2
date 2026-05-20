<?php

declare(strict_types=1);

require_once __DIR__ . '/AdministradorControlador.php';
require_once __DIR__ . '/DocumentoControlador.php';
require_once __DIR__ . '/EstudianteControlador.php';
require_once __DIR__ . '/RespuestaSolicitudControlador.php';
require_once __DIR__ . '/SolicitudControlador.php';
require_once __DIR__ . '/TipoSolicitudControlador.php';

/**
 * Controlador "cliente" (front-controller) muy simple para pruebas.
 *
 * Ejemplo (GET):
 *   ?api=1&tabla=estudiante&accion=listar
 *   ?api=1&tabla=estudiante&accion=buscar&id=1
 *
 * Ejemplo (POST):
 *   api=1&tabla=tipo_solicitud&accion=crear&nombre_tipo=Nuevo
 */
final class ClienteControlador
{
    public function manejar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $tabla = (string)($_REQUEST['tabla'] ?? '');
        $accion = (string)($_REQUEST['accion'] ?? '');
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null;

        $payload = $_POST;

        try {
            $respuesta = $this->dispatch($tabla, $accion, $id, $payload);
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Error interno', 'detail' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    private function dispatch(string $tabla, string $accion, ?int $id, array $payload): array
    {
        $tabla = strtolower(trim($tabla));
        $accion = strtolower(trim($accion));

        return match ($tabla) {
            'administrador' => $this->ejecutar(new AdministradorControlador(), $accion, $id, $payload),
            'documento' => $this->ejecutar(new DocumentoControlador(), $accion, $id, $payload),
            'estudiante' => $this->ejecutar(new EstudianteControlador(), $accion, $id, $payload),
            'respuesta_solicitud' => $this->ejecutar(new RespuestaSolicitudControlador(), $accion, $id, $payload),
            'solicitud' => $this->ejecutar(new SolicitudControlador(), $accion, $id, $payload),
            'tipo_solicitud' => $this->ejecutar(new TipoSolicitudControlador(), $accion, $id, $payload),
            default => ['ok' => false, 'message' => 'Tabla no soportada.'],
        };
    }

    private function ejecutar(object $controller, string $accion, ?int $id, array $payload): array
    {
        return match ($accion) {
            'crear' => $controller->crear($payload),
            'buscar' => $controller->buscar((int)$id),
            'listar' => $controller->listar(),
            'editar' => $controller->editar((int)$id, $payload),
            'eliminar' => $controller->eliminar((int)$id),
            'buscarporsolicitud' => method_exists($controller, 'buscarPorSolicitud')
                ? $controller->buscarPorSolicitud((int)$id)
                : ['ok' => false, 'message' => 'Acción no soportada.'],
            'listarporsolicitud' => method_exists($controller, 'listarPorSolicitud')
                ? $controller->listarPorSolicitud((int)$id)
                : ['ok' => false, 'message' => 'Acción no soportada.'],
            default => ['ok' => false, 'message' => 'Acción no soportada.'],
        };
    }
}

