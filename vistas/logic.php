<?php

$errors = [];
$result = null;
$selectedPanel = $_GET['panel'] ?? 'dashboard';
$currentUserRole = $_SESSION['user_role'] ?? null;
$currentUserId = $_SESSION['user_id'] ?? null;

require_once __DIR__ . '/../modelos/AdministradorModel.php';
require_once __DIR__ . '/../modelos/EstudianteModel.php';
require_once __DIR__ . '/../modelos/SolicitudModel.php';
require_once __DIR__ . '/../config/bootstrap.php';

try {
    ensureAuthSchema();
    ensureSolicitudSchema();
} catch (Throwable $e) {
    // Si falla, el login/registro reportará error más adelante.
}

require_once __DIR__ . '/../modelos/TipoSolicitudModel.php';

// Catálogos
$requestTypes = [];
try {
    $tipoSolicitudModel = new TipoSolicitudModel();
    foreach ($tipoSolicitudModel->listar() as $tipo) {
        $requestTypes[(int)$tipo['id_tipo_solicitud']] = $tipo['nombre_tipo'];
    }
} catch (Throwable $e) {
    // Fallback if DB is not available
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
        13 => 'Otra'
    ];
}

$programs = [
    124 => 'Tecnología en Desarrollo de Software',
    125 => 'Ingeniería de Software',
    121 => 'Administración de Negocios Internacionales',
    123 => 'Técnica Profesional en Soporte Informático',
    143 => 'Técnica Profesional en Procesos Contables Presencial',
    144 => 'Tecnología en Gestión Financiera Presencial',
    146 => 'Tecnología en Gestión Financiera Distancia',
    151 => 'Técnica Profesional en Operaciones Aduaneras y de Comercio Int. Pres',
    153 => 'Administración de Negocios Internacionales Presencial',
    157 => 'Técnica Profesional en Procesos de Diseño de Modas',
    158 => 'Tecnología en Gestión de Diseño de Modas',
    159 => 'Profesional en Diseño y Administración de Negocios de la Moda'
];

$campuses = [
    1 => 'Cúcuta',
    2 => 'Ocaña'
];

$shifts = [
    1 => 'Diurna',
    2 => 'Nocturna',
    3 => 'Distancia',
    4 => 'Virtual'
];

$studentFilter = (string)($_REQUEST['student_filter'] ?? 'all');
$adminPendingFilter = (string)($_REQUEST['admin_pending_filter'] ?? 'all');
$adminHistoryFilter = (string)($_REQUEST['admin_history_filter'] ?? '');

$studentStatusOptions = [
    'all' => 'Todos',
    'Pendiente' => 'Pendiente',
    'Aprobada' => 'Aprobada',
    'Rechazada' => 'Rechazada',
    'Observada' => 'Observada',
    'En espera' => 'En espera',
    'Falta información' => 'Falta información'
];

$adminPendingStatusOptions = [
    'all' => 'Todos',
    'Pendiente' => 'Pendiente',
    'En espera' => 'En espera',
    'Falta información' => 'Falta información'
];

$adminHistoryStatusOptions = [
    'Aprobada' => 'Aprobada',
    'Rechazada' => 'Rechazada',
    'Observada' => 'Observada',
    'Falta información' => 'Falta información',
    'En espera' => 'En espera'
];

function filterRequestsByStatus(array $requests, string $status): array
{
    if ($status === '' || $status === 'all') {
        return $requests;
    }

    return array_values(array_filter($requests, function (array $request) use ($status) {
        return isset($request['estado']) && $request['estado'] === $status;
    }));
}

// Las solicitudes ahora se cargan desde la BD (tabla `solicitud`).

function mapDbSolicitudToUi(array $row): array
{
    return [
        'id' => (int)($row['id_solicitud'] ?? 0),
        'fecha' => (string)($row['fecha'] ?? ''),
        'estado' => (string)($row['estado'] ?? 'Pendiente'),
        'tipo_solicitud_id' => (int)($row['id_tipo_solicitud'] ?? 0),
        'descripcion' => (string)($row['descripcion'] ?? ''),
        'estudiante_id' => (int)($row['id_estudiante'] ?? 0),
        'programa_id' => isset($row['programa_id']) ? (int)$row['programa_id'] : 0,
        'sede_id' => isset($row['sede_id']) ? (int)$row['sede_id'] : 0,
        'jornada_id' => isset($row['jornada_id']) ? (int)$row['jornada_id'] : 0,
        'observacion' => (string)($row['observacion'] ?? ''),
        'admin_id' => array_key_exists('admin_id', $row) && $row['admin_id'] !== null ? (int)$row['admin_id'] : null,
        'respuesta_fecha' => (string)($row['respuesta_fecha'] ?? ''),
        'documento' => (string)($row['documento'] ?? ''),
    ];
}

function formatMoney($value)
{
    return '$' . number_format($value, 0, ',', '.');
}

function formatDate($date)
{
    $timestamp = strtotime($date);
    return $timestamp ? date('d/m/Y', $timestamp) : $date;
}

function getLabel(array $catalog, $id)
{
    return isset($catalog[$id]) ? $catalog[$id] : 'N/D';
}

function getStudent(array $students, $id)
{
    return $students[$id] ?? ['nombre' => 'N/D', 'apellido' => ''];
}

function getAdmin(array $administrators, $id)
{
    return $administrators[$id] ?? ['nombre' => 'N/D'];
}

function findRequestById(array $requests, $id)
{
    foreach ($requests as $request) {
        if ($request['id'] == $id) {
            return $request;
        }
    }
    return null;
}

function badgeClass($status)
{
    return match ($status) {
        'Pendiente',
        'Sin responder' => 'bg-amber-100 text-amber-800',
        'Falta información',
        'En espera' => 'bg-amber-100 text-amber-800',
        'Aprobada' => 'bg-emerald-100 text-emerald-800',
        'Rechazada' => 'bg-red-100 text-red-800',
        'Observada' => 'bg-sky-100 text-sky-800',
        default => 'bg-slate-100 text-slate-700',
    };
}

$currentUserRole = $_SESSION['user_role'] ?? null;
$currentUserId = $_SESSION['user_id'] ?? null;
$currentUser = null;

try {
    $students = [];
    $administrators = [];

    $studentModel = new EstudianteModel();
    foreach ($studentModel->listar() as $row) {
        $students[(int)$row['id_estudiante']] = [
            'id' => (int)$row['id_estudiante'],
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'documento' => $row['documento'],
            'correo' => $row['correo'],
            'telefono' => $row['telefono'],
            'programa' => $row['programa'],
            'semestre' => (int)$row['semestre'],
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

    if ($currentUserRole === 'student' && $currentUserId) {
        $currentUser = $studentModel->buscarPorId((int)$currentUserId);
    }
    if ($currentUserRole === 'admin' && $currentUserId) {
        $currentUser = $adminModel->buscarPorId((int)$currentUserId);
    }
} catch (Throwable $e) {
    $currentUser = null;
    $students = [];
    $administrators = [];
}

$allRequests = [];
$studentRequests = [];
$adminPendingRequests = [];
$adminRespondedRequests = [];
$editRequest = null;
$respondRequest = null;
$replyRequest = null;

function refreshRequestData(): void
{
    global $currentUserRole, $currentUserId, $studentRequests, $adminPendingRequests, $adminRespondedRequests, $allRequests, $studentFilter, $adminPendingFilter, $adminHistoryFilter;

    $studentRequests = [];
    $adminPendingRequests = [];
    $adminRespondedRequests = [];
    $allRequests = [];

    try {
        $solicitudModel = new SolicitudModel();
        if ($currentUserRole === 'student' && $currentUserId) {
            $rows = $solicitudModel->listarPorEstudiante((int)$currentUserId);
            $studentRequests = array_map('mapDbSolicitudToUi', $rows);
            $studentRequests = filterRequestsByStatus($studentRequests, $studentFilter);
            $allRequests = $studentRequests;
        }
        if ($currentUserRole === 'admin') {
            $pendingRows = $solicitudModel->listarPendientes();
            $respondedRows = $solicitudModel->listarRespondidas();
            $adminPendingRequests = array_map('mapDbSolicitudToUi', $pendingRows);
            $adminRespondedRequests = array_map('mapDbSolicitudToUi', $respondedRows);
            $adminPendingRequests = filterRequestsByStatus($adminPendingRequests, $adminPendingFilter);
            $adminRespondedRequests = filterRequestsByStatus($adminRespondedRequests, $adminHistoryFilter);
            $allRequests = array_merge($adminPendingRequests, $adminRespondedRequests);
        }
    } catch (Throwable $e) {
        // Si falla la BD, se verán listas vacías.
    }
}

refreshRequestData();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_request_id']) && $currentUserRole === 'admin') {
    $editRequestId = intval($_GET['edit_request_id'] ?? 0);
    if ($editRequestId > 0) {
        try {
            $model = new SolicitudModel();
            $row = $model->buscarPorId($editRequestId);
            if ($row) {
                $editRequest = mapDbSolicitudToUi($row);
                $selectedPanel = 'admin_reports';
            }
        } catch (Throwable $e) {
            // Ignorar si no se puede cargar la solicitud para edición.
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['respond']) && $currentUserRole === 'admin') {
    $respondId = intval($_GET['respond'] ?? 0);
    if ($respondId > 0) {
        try {
            $model = new SolicitudModel();
            $row = $model->buscarPorId($respondId);
            if ($row) {
                $respondRequest = mapDbSolicitudToUi($row);
                $selectedPanel = 'admin_requests';
            }
        } catch (Throwable $e) {
            // Ignorar si no se puede cargar la solicitud para respuesta.
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reply_request_id']) && $currentUserRole === 'student') {
    $replyRequestId = intval($_GET['reply_request_id'] ?? 0);
    if ($replyRequestId > 0) {
        try {
            $model = new SolicitudModel();
            $row = $model->buscarPorId($replyRequestId);
            if ($row && (int)$row['id_estudiante'] === (int)$currentUserId && !in_array($row['estado'], ['Aprobada', 'Rechazada', 'Pendiente'], true)) {
                $replyRequest = mapDbSolicitudToUi($row);
                $selectedPanel = 'student_requests';
            }
        } catch (Throwable $e) {
            // Ignorar si no se puede cargar la solicitud para respuesta.
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $selectedPanel = $_POST['panel'] ?? $selectedPanel;

    if ($action === 'login') {
        $correo = trim((string)($_POST['correo'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingresa un correo válido.';
        }
        if ($password === '') {
            $errors[] = 'Ingresa tu contraseña.';
        }

        if (empty($errors)) {
            try {
                $studentModel = new EstudianteModel();
                $adminModel = new AdministradorModel();

                $user = $studentModel->buscarPorCorreo($correo);
                $role = 'student';
                $model = $studentModel;
                $idField = 'id_estudiante';

                if (!$user) {
                    $user = $adminModel->buscarPorCorreo($correo);
                    if ($user) {
                        $role = 'admin';
                        $model = $adminModel;
                        $idField = 'id_admin';
                    }
                }

                if (!$user) {
                    $errors[] = 'Correo o contraseña incorrectos.';
                } else {
                    $hashDb = (string)($user['contraseña'] ?? '');
                    $authed = false;

                    if ($hashDb !== '') {
                        $authed = password_verify($password, $hashDb);
                        if (!$authed && hash_equals($hashDb, $password)) {
                            $model->actualizarPassword((int)$user[$idField], password_hash($password, PASSWORD_DEFAULT));
                            $user = $model->buscarPorId((int)$user[$idField]) ?? $user;
                            $authed = true;
                        }
                    } else {
                        if ($password === '123456') {
                            $model->actualizarPassword((int)$user[$idField], password_hash($password, PASSWORD_DEFAULT));
                            $user = $model->buscarPorId((int)$user[$idField]) ?? $user;
                            $authed = true;
                        }
                    }

                    if (!$authed) {
                        $errors[] = 'Correo o contraseña incorrectos.';
                    } else {
                        $_SESSION['user_role'] = $role;
                        $_SESSION['user_id'] = (int)$user[$idField];
                        $currentUserRole = $role;
                        $currentUserId = (int)$user[$idField];
                        $currentUser = $user;
                        $selectedPanel = 'dashboard';
                        refreshRequestData();
                        $fullName = trim(($currentUser['nombre'] ?? '') . ' ' . ($currentUser['apellido'] ?? ''));
                        $result = ['type' => 'info', 'message' => 'Bienvenido, ' . $fullName . '.'];
                    }
                }
            } catch (Throwable $e) {
                $errors[] = 'No se pudo iniciar sesión (revisa la conexión a la BD).';
            }
        }
    }

    if ($action === 'register_student') {
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $apellido = trim((string)($_POST['apellido'] ?? ''));
        $documento = trim((string)($_POST['documento'] ?? ''));
        $correo = trim((string)($_POST['correo'] ?? ''));
        $telefono = trim((string)($_POST['telefono'] ?? ''));
        $programaId = (int)($_POST['programa_id'] ?? 0);
        $semestre = (int)($_POST['semestre'] ?? 0);
        $password = (string)($_POST['password'] ?? '');
        $password2 = (string)($_POST['password2'] ?? '');

        if ($nombre === '' || $apellido === '') $errors[] = 'Ingresa nombre y apellido.';
        if ($documento === '') $errors[] = 'Ingresa tu documento.';
        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ingresa un correo válido.';
        if ($programaId <= 0 || !isset($programs[$programaId])) $errors[] = 'Selecciona un programa válido.';
        if ($semestre < 1 || $semestre > 12) $errors[] = 'Semestre debe estar entre 1 y 12.';
        if ($password === '' || strlen($password) < 6) $errors[] = 'La contraseña debe tener mínimo 6 caracteres.';
        if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';

        if (empty($errors)) {
            try {
                $model = new EstudianteModel();
                if ($model->buscarPorCorreo($correo)) {
                    $errors[] = 'Ya existe una cuenta con ese correo.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $id = $model->crearConPassword(
                        $nombre,
                        $apellido,
                        $documento,
                        $correo,
                        $telefono !== '' ? $telefono : null,
                        (string)$programs[$programaId],
                        $semestre,
                        $hash
                    );
                    $_SESSION['user_role'] = 'student';
                    $_SESSION['user_id'] = (int)$id;
                    $currentUserRole = 'student';
                    $currentUserId = (int)$id;
                    $currentUser = $model->buscarPorId((int)$id);
                    $selectedPanel = 'dashboard';
                    refreshRequestData();
                    $result = ['type' => 'info', 'message' => 'Cuenta creada e inicio de sesión exitoso.'];
                }
            } catch (Throwable $e) {
                $errors[] = 'No se pudo crear la cuenta (revisa la BD).';
            }
        }
    }

    if ($action === 'register_admin') {
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $correo = trim((string)($_POST['correo'] ?? ''));
        $rol = trim((string)($_POST['rol'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $password2 = (string)($_POST['password2'] ?? '');

        if ($nombre === '') $errors[] = 'Ingresa tu nombre.';
        if ($rol === '') $errors[] = 'Ingresa tu rol.';
        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ingresa un correo válido.';
        if ($password === '' || strlen($password) < 6) $errors[] = 'La contraseña debe tener mínimo 6 caracteres.';
        if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';

        if (empty($errors)) {
            try {
                $model = new AdministradorModel();
                if ($model->buscarPorCorreo($correo)) {
                    $errors[] = 'Ya existe una cuenta con ese correo.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $id = $model->crearConPassword($nombre, $correo, $rol, $hash);
                    $_SESSION['user_role'] = 'admin';
                    $_SESSION['user_id'] = (int)$id;
                    $currentUserRole = 'admin';
                    $currentUserId = (int)$id;
                    $currentUser = $model->buscarPorId((int)$id);
                    $selectedPanel = 'dashboard';
                    refreshRequestData();
                    $result = ['type' => 'info', 'message' => 'Cuenta de administrador creada e inicio de sesión exitoso.'];
                }
            } catch (Throwable $e) {
                $errors[] = 'No se pudo crear la cuenta (revisa la BD).';
            }
        }
    }

    if ($action === 'logout') {
        session_unset();
        session_destroy();
        session_start();
        $currentUserRole = null;
        $currentUser = null;
        $selectedPanel = 'login';
    }

    if ($action === 'submit_request' && $currentUserRole === 'student') {
        $requestTypeId = intval($_POST['request_type'] ?? 0);
        $programId = intval($_POST['program'] ?? 0);
        $campusId = intval($_POST['campus'] ?? 0);
        $shiftId = intval($_POST['shift'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($requestTypeId <= 0 || !isset($requestTypes[$requestTypeId])) {
            $errors[] = 'Selecciona un tipo de solicitud válido.';
        }
        if ($programId <= 0 || !isset($programs[$programId])) {
            $errors[] = 'Selecciona un programa válido.';
        }
        if ($campusId <= 0 || !isset($campuses[$campusId])) {
            $errors[] = 'Selecciona una sede válida.';
        }
        if ($shiftId <= 0 || !isset($shifts[$shiftId])) {
            $errors[] = 'Selecciona una jornada válida.';
        }
        if ($description === '') {
            $errors[] = 'Describe brevemente el motivo de la solicitud.';
        }

        // Handle file upload
        $uploadedFile = null;
        if (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['document'];
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Error al subir el archivo.';
            } elseif (!in_array($file['type'], $allowedTypes)) {
                $errors[] = 'Tipo de archivo no permitido. Solo se permiten PDF, DOC, DOCX, JPG, PNG.';
            } elseif ($file['size'] > $maxSize) {
                $errors[] = 'El archivo es demasiado grande. Tamaño máximo: 5MB.';
            } else {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
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
                $newId = $model->crearCompleta(
                    null,
                    'Pendiente',
                    $description,
                    (int)$currentUserId,
                    (int)$requestTypeId,
                    $programId,
                    $campusId,
                    $shiftId,
                    $uploadedFile
                );
                $row = $model->buscarPorId((int)$newId);
                $newRequest = $row ? mapDbSolicitudToUi($row) : [
                    'id' => (int)$newId,
                    'fecha' => date('Y-m-d'),
                    'estado' => 'Pendiente',
                    'tipo_solicitud_id' => $requestTypeId,
                    'descripcion' => $description,
                    'estudiante_id' => (int)$currentUserId,
                    'programa_id' => $programId,
                    'sede_id' => $campusId,
                    'jornada_id' => $shiftId,
                    'observacion' => '',
                    'admin_id' => null,
                    'respuesta_fecha' => '',
                    'documento' => $uploadedFile
                ];

                $studentRequests = array_merge([$newRequest], $studentRequests);
                refreshRequestData();
                $result = [
                    'type' => 'student_request',
                    'message' => 'Tu solicitud ha sido registrada correctamente.',
                    'request' => $newRequest
                ];
                $selectedPanel = 'student_requests';
            } catch (Throwable $e) {
                $errors[] = 'No se pudo registrar la solicitud: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'submit_response' && $currentUserRole === 'admin') {
        $requestId = intval($_POST['request_id'] ?? 0);
        $responseState = $_POST['response_state'] ?? '';
        $observation = trim($_POST['response_observation'] ?? '');

        if ($requestId > 0) {
            try {
                $model = new SolicitudModel();
                $row = $model->buscarPorId($requestId);
                if ($row) {
                    $respondRequest = mapDbSolicitudToUi($row);
                }
            } catch (Throwable $e) {
                // Ignorar si no se puede cargar la solicitud.
            }
        }

        if ($requestId <= 0) {
            $errors[] = 'Selecciona una solicitud para responder.';
        }
        if ($responseState === '') {
            $errors[] = 'Selecciona el estado final de la solicitud.';
        }
        if ($observation === '') {
            $errors[] = 'Agrega una observación a la respuesta.';
        }

        if (empty($errors)) {
            try {
                $model = new SolicitudModel();
                $existing = $model->buscarPorId((int)$requestId);
                if (!$existing) {
                    $errors[] = 'No se encontró la solicitud seleccionada.';
                } else {
                    $ok = $model->responder((int)$requestId, (string)$responseState, (string)$observation, (int)$currentUserId);
                    if (!$ok) {
                        $errors[] = 'No se pudo guardar la respuesta.';
                    } else {
                        $updated = $model->buscarPorId((int)$requestId);
                        $updatedRequest = $updated ? mapDbSolicitudToUi($updated) : ['id' => (int)$requestId];
                        $result = [
                            'type' => 'admin_response',
                            'message' => 'Respuesta registrada con éxito para la solicitud seleccionada.',
                            'request' => $updatedRequest
                        ];
                        $selectedPanel = 'admin_requests';
                        refreshRequestData();
                        $respondRequest = null;
                    }
                }
            } catch (Throwable $e) {
                $errors[] = 'No se pudo registrar la respuesta (revisa la BD).';
            }
        }
    }

    if ($action === 'submit_student_reply' && $currentUserRole === 'student') {
        $requestId = intval($_POST['request_id'] ?? 0);
        $studentResponse = trim($_POST['student_response'] ?? '');

        if ($requestId <= 0) {
            $errors[] = 'Selecciona una solicitud válida para responder.';
        }
        if ($studentResponse === '') {
            $errors[] = 'Escribe tu respuesta antes de enviarla.';
        }

        if (empty($errors)) {
            try {
                $model = new SolicitudModel();
                $existing = $model->buscarPorId($requestId);
                if (!$existing || (int)$existing['id_estudiante'] !== (int)$currentUserId) {
                    $errors[] = 'No se encontró la solicitud seleccionada.';
                } elseif (in_array($existing['estado'], ['Aprobada', 'Rechazada'], true)) {
                    $errors[] = 'No puedes responder una solicitud que ya fue aprobada o rechazada.';
                } else {
                    $ok = $model->enviarRespuestaEstudiante($requestId, $studentResponse);
                    if (!$ok) {
                        $errors[] = 'No se pudo enviar la respuesta. Inténtalo de nuevo.';
                    } else {
                        $updated = $model->buscarPorId($requestId);
                        $updatedRequest = $updated ? mapDbSolicitudToUi($updated) : ['id' => $requestId];
                        $result = [
                            'type' => 'info',
                            'message' => 'Tu respuesta fue enviada al administrador y la solicitud pasó a En espera.',
                            'request' => $updatedRequest
                        ];
                        $selectedPanel = 'student_requests';
                        refreshRequestData();
                        $replyRequest = null;
                    }
                }
            } catch (Throwable $e) {
                $errors[] = 'No se pudo enviar la respuesta. Inténtalo de nuevo.';
            }
        }
    }

    if ($action === 'delete_request' && $currentUserRole === 'admin') {
        $requestId = intval($_POST['request_id'] ?? 0);
        if ($requestId <= 0) {
            $errors[] = 'Solicitud inválida para eliminar.';
        }
        if (empty($errors)) {
            try {
                $model = new SolicitudModel();
                if ($model->eliminar($requestId)) {
                    $result = ['type' => 'info', 'message' => 'Solicitud eliminada correctamente.'];
                } else {
                    $errors[] = 'No se pudo eliminar la solicitud.';
                }
            } catch (Throwable $e) {
                $errors[] = 'Error al eliminar la solicitud: ' . $e->getMessage();
            }
            refreshRequestData();
            $selectedPanel = 'admin_reports';
        }
    }

    if ($action === 'update_request' && $currentUserRole === 'admin') {
        $requestId = intval($_POST['request_id'] ?? 0);
        $estado = $_POST['request_estado'] ?? '';
        $observacion = trim($_POST['request_observacion'] ?? '');

        if ($requestId <= 0) {
            $errors[] = 'Solicitud inválida para actualizar.';
        }
        if ($estado === '') {
            $errors[] = 'Selecciona un estado para la solicitud.';
        }

        if (empty($errors)) {
            try {
                $model = new SolicitudModel();
                $existing = $model->buscarPorId($requestId);
                if (!$existing) {
                    $errors[] = 'No se encontró la solicitud seleccionada.';
                } else {
                    $ok = $model->responder($requestId, $estado, $observacion, (int)$currentUserId);
                    if ($ok) {
                        $updated = $model->buscarPorId($requestId);
                        $editRequest = $updated ? mapDbSolicitudToUi($updated) : null;
                        $result = ['type' => 'info', 'message' => 'Solicitud actualizada correctamente.'];
                    } else {
                        $errors[] = 'No se pudo actualizar la solicitud.';
                    }
                }
            } catch (Throwable $e) {
                $errors[] = 'Error al actualizar la solicitud: ' . $e->getMessage();
            }
            refreshRequestData();
            $selectedPanel = 'admin_reports';
        }
    }
}

if (!isset($_GET['panel']) && !isset($_POST['panel'])) {
    if (!$currentUser) {
        $selectedPanel = 'login';
    }
}

$pendingCount = 0;
$respondedCount = 0;
foreach ($allRequests as $request) {
    if ($request['estado'] === 'Pendiente' || $request['estado'] === 'Falta información' || $request['estado'] === 'En espera' || $request['estado'] === 'Sin responder') {
        $pendingCount++;
    } else {
        $respondedCount++;
    }
}

$studentRequestCount = count($studentRequests);
?>
