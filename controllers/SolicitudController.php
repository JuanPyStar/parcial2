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
            117 => 'Técnica Profesional en Operaciones Logísticas',
            118 => 'Tecnología en Gestión Logística Empresarial',
            119 => 'Técnica Profesional en Producción Gráfica',
            120 => 'Tecn. en Gestión de Contenidos Gráficos Publicitarios',
            121 => 'Diseño Gráfico',
            123 => 'Técnica Profesional en Soporte Informático',
            124 => 'Tecnología en Desarrollo de Software',
            125 => 'Ingeniería de Software',
            126 => 'Especialización en Gestión Pública',
            127 => 'Tecn. en Gestión de Contenidos Gráficos Public. Ocaña',
            128 => 'Diseño Gráfico Ocaña',
            130 => 'Tecn. en Gestión de Negocios Internacionales Ocaña',
            131 => 'Administración de Negocios Internacionales Ocaña',
            133 => 'Técnica Prof. en Operaciones Turísticas Virtual',
            134 => 'Tecnología en Gestión de Turismo Sostenible Virtual',
            135 => 'Profesional en Administración Turística y Hotelera Virtual',
            136 => 'Técnica Prof. en Operaciones Turísticas Presencial',
            137 => 'Tecnología en Gestión del Turismo Sostenible Presencial',
            138 => 'Profesional en Administración Turística y Hotelera Presencial',
            143 => 'Técnica Profesional en Procesos Contables Presencial',
            144 => 'Tecnología en Gestión Financiera Presencial',
            145 => 'Administración Financiera Presencial',
            146 => 'Técnica Profesional en Procesos Contables Distancia',
            147 => 'Tecnología en Gestión Financiera Distancia',
            148 => 'Administración Financiera Distancia',
            151 => 'Técnica Prof. en Operaciones Aduaneras y de Comercio Int. Pres',
            152 => 'Tecnología en Gestión de Marketing de Negocios Int. Pres',
            153 => 'Administración de Negocios Internacionales Presencial',
            154 => 'Técnica Prof. en Operaciones Aduaneras y Comercio Int. Dist',
            155 => 'Tecnología en Gestión de Marketing de Negocios Int. Dist',
            156 => 'Administración de Negocios Internacionales Distancia',
            157 => 'Técnica Profesional en Procesos de Diseño de Modas',
            158 => 'Tecnología en Gestión de Diseño de Modas',
            159 => 'Profesional en Diseño y Administración de Negocios de la Moda',
            162 => 'Técnica Prof. en Operaciones Aduaneras y Comercio Int. Virtual',
            163 => 'Tecnología en Gestión de Comercio Internacional Virtual',
            164 => 'Administración de Negocios Internacionales Virtual',
            165 => 'Especialización en Analítica de Datos para los Negocios Pres',
            166 => 'Especialización en Analítica de Datos para los Negocios Virtual',
            167 => 'Técnica Profesional en Operaciones Logísticas',
            168 => 'Tecnología en Gestión Logística Empresarial',
            169 => 'Profesional en Administración Logística Internacional',
            174 => 'Técnico Profesional en Producción Gráfica',
            175 => 'Tecn. en Gestión de Contenidos Gráficos Publicitarios',
            176 => 'Profesional en Diseño Gráfico',
            177 => 'Especialización en Marketing Digital Estratégico Presencial',
            179 => 'Especialización en Marketing Digital Estratégico Virtual',
            75 => 'Administración Financiera - Ocaña',
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
            if (count($requestTypes) < 13) {
                $requestTypes = [
                    1 => 'Cancelación de semestre',
                    2 => 'Curso dirigido',
                    3 => 'Cancelación de asignaturas',
                    4 => 'Cambio de jornada',
                    5 => 'Transferencia interna',
                    6 => 'Examen de validación por suficiencia',
                    7 => 'Reingreso',
                    8 => 'Matrícula mínima de créditos',
                    9 => 'Traslado de sede',
                    10 => 'Pago de créditos adicionales',
                    11 => 'Constancia de estudio',
                    12 => 'Certificado de notas',
                    13 => 'Otra',
                ];
            }
        } catch (Throwable $e) {
            $requestTypes = [
                1 => 'Cancelación de semestre',
                2 => 'Curso dirigido',
                3 => 'Cancelación de asignaturas',
                4 => 'Cambio de jornada',
                5 => 'Transferencia interna',
                6 => 'Examen de validación por suficiencia',
                7 => 'Reingreso',
                8 => 'Matrícula mínima de créditos',
                9 => 'Traslado de sede',
                10 => 'Pago de créditos adicionales',
                11 => 'Constancia de estudio',
                12 => 'Certificado de notas',
                13 => 'Otra',
            ];
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
        $adminPendingDate = trim((string)($_REQUEST['admin_pending_date'] ?? ''));
        $adminPendingDocument = trim((string)($_REQUEST['admin_pending_document'] ?? ''));
        $adminHistoryFilter = (string)($_REQUEST['admin_history_filter'] ?? 'all');
        $adminHistoryDate = trim((string)($_REQUEST['admin_history_date'] ?? ''));
        $adminHistoryDocument = trim((string)($_REQUEST['admin_history_document'] ?? ''));

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
                $adminPendingRequests = filterRequestsByDate($adminPendingRequests, $adminPendingDate);
                $adminPendingRequests = filterRequestsByDocument($adminPendingRequests, $adminPendingDocument, $students);
                $adminRespondedRequests = filterRequestsByStatus($adminRespondedRequests, $adminHistoryFilter);
                $adminRespondedRequests = filterRequestsByDate($adminRespondedRequests, $adminHistoryDate);
                $adminRespondedRequests = filterRequestsByDocument($adminRespondedRequests, $adminHistoryDocument, $students);
                $allRequests = array_merge($adminPendingRequests, $adminRespondedRequests);
            }
        } catch (Throwable $e) {
            // ignore DB errors
        }

        // Handle GET actions: edit/respond/view/reply
        $editRequest = null; $respondRequest = null; $replyRequest = null; $viewRequest = null;
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
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['view_request_id']) && $currentUserRole === 'student') {
            $id = intval($_GET['view_request_id']);
            if ($id > 0) {
                try { $row = (new SolicitudModel())->buscarPorId($id); if ($row && (int)$row['id_estudiante'] === (int)$currentUserId) $viewRequest = mapDbSolicitudToUi($row); } catch (Throwable $e) {}
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
                        $uploadDir = __DIR__ . '/../uploads/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        $fileName = uniqid() . '_' . basename($file['name']);
                        $filePath = $uploadDir . $fileName;
                        if (move_uploaded_file($file['tmp_name'], $filePath)) {
                            $uploadedFile = $fileName;
                        } else {
                            $errors[] = 'Error al guardar el archivo.';
                        }
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
                $uploadedFile = null;
                if (isset($_FILES['response_document']) && $_FILES['response_document']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $file = $_FILES['response_document'];
                    $allowedTypes = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/jpeg','image/png'];
                    $maxSize = 5 * 1024 * 1024;
                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        $errors[] = 'Error al subir el archivo.';
                    } elseif (!in_array($file['type'], $allowedTypes)) {
                        $errors[] = 'Tipo de archivo no permitido.';
                    } elseif ($file['size'] > $maxSize) {
                        $errors[] = 'El archivo es demasiado grande.';
                    } else {
                        $uploadDir = __DIR__ . '/../uploads/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        $fileName = uniqid() . '_' . basename($file['name']);
                        $filePath = $uploadDir . $fileName;
                        if (move_uploaded_file($file['tmp_name'], $filePath)) {
                            $uploadedFile = $fileName;
                        } else {
                            $errors[] = 'Error al guardar el archivo.';
                        }
                    }
                }

                if ($requestId <= 0) $errors[] = 'Selecciona una solicitud válida para responder.';
                if ($studentResponse === '') $errors[] = 'Escribe tu respuesta antes de enviarla.';
                if (empty($errors)) {
                    try {
                        $model = new SolicitudModel();
                        $existing = $model->buscarPorId($requestId);
                        if (!$existing || (int)$existing['id_estudiante'] !== (int)$currentUserId) {
                            $errors[] = 'No se encontró la solicitud seleccionada.';
                        } elseif (in_array($existing['estado'], ['Aprobada','Rechazada'], true)) {
                            $errors[] = 'No puedes responder una solicitud que ya fue aprobada o rechazada.';
                        } else {
                            $ok = $model->enviarRespuestaEstudiante($requestId, $studentResponse, $uploadedFile);
                            if (!$ok) {
                                $errors[] = 'No se pudo enviar la respuesta.';
                            } else {
                                $result = ['type'=>'info','message'=>'Tu respuesta fue enviada al administrador.'];
                                $rows = (new SolicitudModel())->listarPorEstudiante((int)$currentUserId);
                                $studentRequests = array_map('mapDbSolicitudToUi',$rows);
                            }
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
                'dashboard' => 'estudiante/dashboard',
                'new_request' => 'estudiante/new_request',
                'student_requests' => 'estudiante/requests',
                'profile' => 'estudiante/profile',
                'help' => 'estudiante/help',
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
            'viewRequest' => $viewRequest,
            'studentFilter' => $studentFilter,
            'adminPendingFilter' => $adminPendingFilter,
            'adminPendingDate' => $adminPendingDate,
            'adminPendingDocument' => $adminPendingDocument,
            'adminHistoryFilter' => $adminHistoryFilter,
            'adminHistoryDate' => $adminHistoryDate,
            'adminHistoryDocument' => $adminHistoryDocument,
            'studentStatusOptions' => [
                'all' => 'Todos', 'Pendiente' => 'Pendiente', 'Aprobada' => 'Aprobada'
            ],
            'adminPendingStatusOptions' => ['all' => 'Todos', 'Pendiente' => 'Pendiente'],
        ];

        $view = $viewMap[$panel] ?? ($currentUserRole === 'student' ? 'estudiante/dashboard' : 'admin/dashboard');
        $this->render($view, $data);
    }
}
