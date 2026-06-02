<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../modelos/SolicitudModel.php';
require_once __DIR__ . '/../modelos/EstudianteModel.php';
require_once __DIR__ . '/../modelos/AdministradorModel.php';
require_once __DIR__ . '/../modelos/TipoSolicitudModel.php';

class SolicitudController extends BaseController
{
    private array $programs = [];
    private array $campuses = [];
    private array $shifts = [];

    public function __construct()
    {
        $this->programs = [
            124 => 'Tecnología en Desarrollo de Software',
            125 => 'Ingeniería de Software',
            121 => 'Administración de Negocios Internacionales',
        ];
        $this->campuses = [1 => 'Cúcuta', 2 => 'Ocaña'];
        $this->shifts = [1 => 'Diurna', 2 => 'Nocturna', 3 => 'Distancia', 4 => 'Virtual'];
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function dispatch(string $action = 'index')
    {
        $currentUserRole = $_SESSION['user_role'] ?? null;
        $currentUserId = $_SESSION['user_id'] ?? null;

        // Load catalogs
        try {
            $tipoSolicitudModel = new TipoSolicitudModel();
            $requestTypes = [];
            foreach ($tipoSolicitudModel->listar() as $tipo) {
                $requestTypes[(int)$tipo['id_tipo_solicitud']] = $tipo['nombre_tipo'];
            }
        } catch (Throwable $e) {
            $requestTypes = [1 => 'Cancelación de semestre', 2 => 'Curso dirigido', 3 => 'Cancelación de asignaturas'];
        }

        // Load students and admins
        $students = [];
        $administrators = [];
        try {
            $studentModel = new EstudianteModel();
            foreach ($studentModel->listar() as $row) {
                $students[(int)$row['id_estudiante']] = [
                    'id' => (int)$row['id_estudiante'],
                    'nombre' => $row['nombre'],
                    'apellido' => $row['apellido'],
                    'documento' => $row['documento'] ?? '',
                    'correo' => $row['correo'] ?? '',
                    'telefono' => $row['telefono'] ?? '',
                    'programa' => $row['programa'] ?? '',
                    'semestre' => isset($row['semestre']) ? (int)$row['semestre'] : 0,
                ];
            }
            $adminModel = new AdministradorModel();
            foreach ($adminModel->listar() as $row) {
                $administrators[(int)$row['id_admin']] = [
                    'id' => (int)$row['id_admin'],
                    'nombre' => $row['nombre'],
                    'correo' => $row['correo'],
                    'rol' => $row['rol'],
                ];
            }
        } catch (Throwable $e) {
            // ignore
        }

        // Refresh request lists
        $studentFilter = (string)($_REQUEST['student_filter'] ?? 'all');
        $adminPendingFilter = (string)($_REQUEST['admin_pending_filter'] ?? 'all');
        $adminHistoryFilter = (string)($_REQUEST['admin_history_filter'] ?? '');

        $studentRequests = [];
        $adminPendingRequests = [];
        $adminRespondedRequests = [];
        $allRequests = [];

        try {
            $solModel = new SolicitudModel();
            if ($currentUserRole === 'student' && $currentUserId) {
                $rows = $solModel->listarPorEstudiante((int)$currentUserId);
                $studentRequests = array_map('mapDbSolicitudToUi', $rows);
                $studentRequests = filterRequestsByStatus($studentRequests, $studentFilter);
                $allRequests = $studentRequests;
            }
            if ($currentUserRole === 'admin') {
                $pendingRows = $solModel->listarPendientes();
                $respondedRows = $solModel->listarRespondidas();
                $adminPendingRequests = array_map('mapDbSolicitudToUi', $pendingRows);
                $adminRespondedRequests = array_map('mapDbSolicitudToUi', $respondedRows);
                $adminPendingRequests = filterRequestsByStatus($adminPendingRequests, $adminPendingFilter);
                $adminRespondedRequests = filterRequestsByStatus($adminRespondedRequests, $adminHistoryFilter);
                $allRequests = array_merge($adminPendingRequests, $adminRespondedRequests);
            }
        } catch (Throwable $e) {
            // ignore DB errors
        }

        // Handle GET actions: edit/respond/reply
        $editRequest = null; $respondRequest = null; $replyRequest = null;
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_request_id']) && $currentUserRole === 'admin') {
            $id = intval($_GET['edit_request_id']);
            if ($id > 0) {
                try { $row = (new SolicitudModel())->buscarPorId($id); if ($row) $editRequest = mapDbSolicitudToUi($row); } catch (Throwable $e) {}
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['respond']) && $currentUserRole === 'admin') {
            $id = intval($_GET['respond']);
            if ($id > 0) {
                try { $row = (new SolicitudModel())->buscarPorId($id); if ($row) $respondRequest = mapDbSolicitudToUi($row); } catch (Throwable $e) {}
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reply_request_id']) && $currentUserRole === 'student') {
            $id = intval($_GET['reply_request_id']);
            if ($id > 0) {
                try { $row = (new SolicitudModel())->buscarPorId($id); if ($row && (int)$row['id_estudiante'] === (int)$currentUserId) $replyRequest = mapDbSolicitudToUi($row); } catch (Throwable $e) {}
            }
        }

        // Handle POST actions moved from previous logic
        $errors = [];
        $result = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actionPost = $_POST['action'] ?? '';
            if ($actionPost === 'submit_request' && $currentUserRole === 'student') {
                $requestTypeId = intval($_POST['request_type'] ?? 0);
                $programId = intval($_POST['program'] ?? 0);
                $campusId = intval($_POST['campus'] ?? 0);
                $shiftId = intval($_POST['shift'] ?? 0);
                $description = trim($_POST['description'] ?? '');

                if ($requestTypeId <= 0) $errors[] = 'Selecciona un tipo de solicitud válido.';
                if ($programId <= 0) $errors[] = 'Selecciona un programa válido.';
                if ($campusId <= 0) $errors[] = 'Selecciona una sede válida.';
                if ($shiftId <= 0) $errors[] = 'Selecciona una jornada válida.';
                if ($description === '') $errors[] = 'Describe brevemente el motivo de la solicitud.';

                // handle upload
                $uploadedFile = null;
                if (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $file = $_FILES['document'];
                    $allowedTypes = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/jpeg','image/png'];
                    $maxSize = 5 * 1024 * 1024;
                    if ($file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Error al subir el archivo.';
                    elseif (!in_array($file['type'], $allowedTypes)) $errors[] = 'Tipo de archivo no permitido.';
                    elseif ($file['size'] > $maxSize) $errors[] = 'El archivo es demasiado grande.';
                    else {
                        $uploadDir = __DIR__ . '/../public/uploads/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        $fileName = uniqid() . '_' . basename($file['name']);
                        $filePath = $uploadDir . $fileName;
                        if (move_uploaded_file($file['tmp_name'], $filePath)) $uploadedFile = $fileName; else $errors[] = 'Error al guardar el archivo.';
                    }
                }

                if (empty($errors)) {
                    try {
                        $model = new SolicitudModel();
                        $newId = $model->crearCompleta(null,'Pendiente',$description,(int)$currentUserId,(int)$requestTypeId,$programId,$campusId,$shiftId,$uploadedFile);
                        $row = $model->buscarPorId((int)$newId);
                        $newRequest = $row ? mapDbSolicitudToUi($row) : ['id'=>$newId,'fecha'=>date('Y-m-d'),'estado'=>'Pendiente','tipo_solicitud_id'=>$requestTypeId,'descripcion'=>$description,'estudiante_id'=>(int)$currentUserId,'programa_id'=>$programId,'sede_id'=>$campusId,'jornada_id'=>$shiftId,'observacion'=>'','admin_id'=>null,'respuesta_fecha'=>'','documento'=>$uploadedFile];
                        $result = ['type'=>'student_request','message'=>'Tu solicitud ha sido registrada correctamente.','request'=>$newRequest];
                        // refresh lists
                        $rows = (new SolicitudModel())->listarPorEstudiante((int)$currentUserId);
                        $studentRequests = array_map('mapDbSolicitudToUi',$rows);
                    } catch (Throwable $e) {
                        $errors[] = 'No se pudo registrar la solicitud: ' . $e->getMessage();
                    }
                }
            }

            if ($actionPost === 'submit_response' && $currentUserRole === 'admin') {
                $requestId = intval($_POST['request_id'] ?? 0);
                $responseState = $_POST['response_state'] ?? '';
                $observation = trim($_POST['response_observation'] ?? '');

                if ($requestId <= 0) $errors[] = 'Selecciona una solicitud para responder.';
                if ($responseState === '') $errors[] = 'Selecciona el estado final de la solicitud.';
                if ($observation === '') $errors[] = 'Agrega una observación a la respuesta.';

                if (empty($errors)) {
                    try {
                        $model = new SolicitudModel();
                        $existing = $model->buscarPorId((int)$requestId);
                        if (!$existing) $errors[] = 'No se encontró la solicitud seleccionada.';
                        else {
                            $ok = $model->responder((int)$requestId, (string)$responseState, (string)$observation, (int)$currentUserId);
                            if (!$ok) $errors[] = 'No se pudo guardar la respuesta.';
                            else {
                                $updated = $model->buscarPorId((int)$requestId);
                                $result = ['type'=>'admin_response','message'=>'Respuesta registrada con éxito.','request'=>$updated ? mapDbSolicitudToUi($updated) : ['id'=>$requestId]];
                            }
                        }
                    } catch (Throwable $e) {
                        $errors[] = 'No se pudo registrar la respuesta (revisa la BD).';
                    }
                }
            }

            if ($actionPost === 'submit_student_reply' && $currentUserRole === 'student') {
                $requestId = intval($_POST['request_id'] ?? 0);
                $studentResponse = trim($_POST['student_response'] ?? '');
                if ($requestId <= 0) $errors[] = 'Selecciona una solicitud válida para responder.';
                if ($studentResponse === '') $errors[] = 'Escribe tu respuesta antes de enviarla.';
                if (empty($errors)) {
                    try {
                        $model = new SolicitudModel();
                        $existing = $model->buscarPorId($requestId);
                        if (!$existing || (int)$existing['id_estudiante'] !== (int)$currentUserId) $errors[] = 'No se encontró la solicitud seleccionada.';
                        elseif (in_array($existing['estado'], ['Aprobada','Rechazada'], true)) $errors[] = 'No puedes responder una solicitud que ya fue aprobada o rechazada.';
                        else {
                            $ok = $model->enviarRespuestaEstudiante($requestId,$studentResponse);
                            if (!$ok) $errors[] = 'No se pudo enviar la respuesta.';
                            else $result = ['type'=>'info','message'=>'Tu respuesta fue enviada al administrador.'];
                        }
                    } catch (Throwable $e) { $errors[] = 'No se pudo enviar la respuesta. Inténtalo de nuevo.'; }
                }
            }

            if ($actionPost === 'delete_request' && $currentUserRole === 'admin') {
                $requestId = intval($_POST['request_id'] ?? 0);
                if ($requestId <= 0) $errors[] = 'Solicitud inválida para eliminar.';
                if (empty($errors)) {
                    try { $model = new SolicitudModel(); if ($model->eliminar($requestId)) $result = ['type'=>'info','message'=>'Solicitud eliminada correctamente.']; else $errors[] = 'No se pudo eliminar la solicitud.'; } catch (Throwable $e) { $errors[] = 'Error al eliminar la solicitud.'; }
                }
            }

            if ($actionPost === 'update_request' && $currentUserRole === 'admin') {
                $requestId = intval($_POST['request_id'] ?? 0);
                $estado = $_POST['request_estado'] ?? '';
                $observacion = trim($_POST['request_observacion'] ?? '');
                if ($requestId <= 0) $errors[] = 'Solicitud inválida para actualizar.';
                if ($estado === '') $errors[] = 'Selecciona un estado para la solicitud.';
                if (empty($errors)) {
                    try {
                        $model = new SolicitudModel(); $existing = $model->buscarPorId($requestId);
                        if (!$existing) $errors[] = 'No se encontró la solicitud seleccionada.';
                        else { $ok = $model->responder($requestId,$estado,$observacion,(int)$currentUserId); if ($ok) $result = ['type'=>'info','message'=>'Solicitud actualizada correctamente.']; else $errors[] = 'No se pudo actualizar la solicitud.'; }
                    } catch (Throwable $e) { $errors[] = 'Error al actualizar la solicitud.'; }
                }
            }
        }

        // Counts
        $pendingCount = 0; $respondedCount = 0;
        foreach ($allRequests as $request) {
            if (in_array($request['estado'], ['Pendiente','Falta información','En espera','Sin responder'], true)) {
                $pendingCount++;
            } else {
                $respondedCount++;
            }
        }

        $panel = (string)($_REQUEST['panel'] ?? 'dashboard');
        if ($currentUserRole === null) {
            $this->redirect('index.php?controller=Auth&action=login');
            return;
        }

        $viewMap = [];
        if ($currentUserRole === 'student') {
            $viewMap = [
                'dashboard' => 'student/dashboard',
                'new_request' => 'student/new_request',
                'student_requests' => 'student/requests',
                'profile' => 'student/profile',
                'help' => 'student/help',
            ];
        } elseif ($currentUserRole === 'admin') {
            $viewMap = [
                'dashboard' => 'admin/dashboard',
                'admin_requests' => 'admin/requests',
                'admin_reports' => 'admin/reports',
                'profile' => 'admin/profile',
                'help' => 'admin/help',
            ];
        }

        $data = [
            'currentUser' => $_SESSION['current_user'] ?? null,
            'currentUserRole' => $currentUserRole,
            'selectedPanel' => $panel,
            'errors' => $errors,
            'result' => $result,
            'old' => $_POST ?? [],
            'get' => $_GET ?? [],
            'requestTypes' => $requestTypes,
            'programs' => $this->programs,
            'campuses' => $this->campuses,
            'shifts' => $this->shifts,
            'studentRequests' => $studentRequests,
            'adminPendingRequests' => $adminPendingRequests,
            'adminRespondedRequests' => $adminRespondedRequests,
            'allRequests' => $allRequests,
            'students' => $students,
            'administrators' => $administrators,
            'pendingCount' => $pendingCount,
            'respondedCount' => $respondedCount,
            'editRequest' => $editRequest,
            'respondRequest' => $respondRequest,
            'replyRequest' => $replyRequest,
            'studentFilter' => $studentFilter,
            'adminPendingFilter' => $adminPendingFilter,
            'adminHistoryFilter' => $adminHistoryFilter,
            'studentStatusOptions' => [
                'all' => 'Todos', 'Pendiente' => 'Pendiente', 'Aprobada' => 'Aprobada'
            ],
            'adminPendingStatusOptions' => ['all' => 'Todos', 'Pendiente' => 'Pendiente'],
        ];

        $view = $viewMap[$panel] ?? ($currentUserRole === 'student' ? 'student/dashboard' : 'admin/dashboard');
        $this->render($view, $data);
    }
}
