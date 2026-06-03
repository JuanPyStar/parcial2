<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../modelos/EstudianteModel.php';
require_once __DIR__ . '/../modelos/AdministradorModel.php';

class AuthController extends BaseController
{
    public function dispatch(string $action = 'login')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $currentRole = $_SESSION['user_role'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = $_POST['action'] ?? '';
            if ($task === 'login') {
                $this->login($_POST);
                return;
            }
            if (in_array($task, ['register_student','register_admin'], true)) {
                // Only allow registration actions for authenticated admins
                if ($currentRole !== 'admin') {
                    $this->render('auth/login', ['errors' => ['No autorizado.'], 'old' => []]);
                    return;
                }
                if ($task === 'register_student') {
                    $this->registerStudent($_POST);
                    return;
                }
                if ($task === 'register_admin') {
                    $this->registerAdmin($_POST);
                    return;
                }
            }
            if ($task === 'logout') {
                $this->logout();
                return;
            }
        }

        if ($action === 'register') {
            // Only admins can access the register page
            if ($currentRole !== 'admin') {
                $this->redirect('index.php?controller=Auth&action=login');
                return;
            }
            $this->render('auth/register', ['errors' => [], 'old' => [], 'selectedPanel' => 'register']);
            return;
        }

        $this->render('auth/login', ['errors' => [], 'old' => []]);
    }

    private function login(array $input): void
    {
        $errors = [];
        $correo = trim((string)($input['correo'] ?? ''));
        $password = (string)($input['password'] ?? '');

        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingresa un correo válido.';
        }
        if ($password === '') {
            $errors[] = 'Ingresa tu contraseña.';
        }

        if (!empty($errors)) {
            $this->render('auth/login', ['errors' => $errors, 'old' => $input]);
            return;
        }

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
                    $_SESSION['current_user'] = $user;
                    $this->redirect('index.php?controller=Solicitud&action=index');
                    return;
                }
            }
        } catch (Throwable $e) {
            $errors[] = 'No se pudo iniciar sesión (revisa la conexión a la BD).';
        }

        $this->render('auth/login', ['errors' => $errors, 'old' => $input]);
    }

    private function registerStudent(array $input): void
    {
        $errors = [];
        $nombre = trim((string)($input['nombre'] ?? ''));
        $apellido = trim((string)($input['apellido'] ?? ''));
        $documento = trim((string)($input['documento'] ?? ''));
        $correo = trim((string)($input['correo'] ?? ''));
        $telefono = trim((string)($input['telefono'] ?? ''));
        $programa = trim((string)($input['programa'] ?? ''));
        $semestre = (int)($input['semestre'] ?? 0);
        $password = (string)($input['password'] ?? '');
        $password2 = (string)($input['password2'] ?? '');

        $allowedPrograms = [
            'Ingeniería de Software',
            'Diseño Gráfico',
            'Negocios Internacionales',
            'Diseño de Modas',
            'Financiera',
        ];

        if ($nombre === '' || $apellido === '') $errors[] = 'Ingresa nombre y apellido.';
        if ($documento === '') $errors[] = 'Ingresa tu documento.';
        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ingresa un correo válido.';
        if ($programa === '' || !in_array($programa, $allowedPrograms, true)) $errors[] = 'Selecciona un programa válido.';
        if ($semestre <= 0) $errors[] = 'Ingresa un semestre válido.';
        if ($password === '' || strlen($password) < 6) $errors[] = 'La contraseña debe tener mínimo 6 caracteres.';
        if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';

        if (!empty($errors)) {
            $this->render('auth/register', ['errors' => $errors, 'old' => $input]);
            return;
        }

        try {
            $model = new EstudianteModel();
            if ($model->buscarPorCorreo($correo)) {
                $errors[] = 'Ya existe una cuenta con ese correo.';
                $this->render('auth/register', ['errors' => $errors, 'old' => $input]);
                return;
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $id = $model->crearConPassword(
                $nombre,
                $apellido,
                $documento,
                $correo,
                $telefono !== '' ? $telefono : null,
                (string)($input['programa'] ?? ''),
                $semestre,
                $hash
            );
            $_SESSION['user_role'] = 'student';
            $_SESSION['user_id'] = (int)$id;
            $_SESSION['current_user'] = $model->buscarPorId((int)$id);
            $this->redirect('index.php?controller=Solicitud&action=index');
            return;
        } catch (Throwable $e) {
            $errors[] = 'No se pudo crear la cuenta (revisa la BD).';
            $this->render('auth/register', ['errors' => $errors, 'old' => $input]);
            return;
        }
    }

    private function registerAdmin(array $input): void
    {
        $errors = [];
        $nombre = trim((string)($input['nombre'] ?? ''));
        $correo = trim((string)($input['correo'] ?? ''));
        $rol = trim((string)($input['rol'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $password2 = (string)($input['password2'] ?? '');

        if ($nombre === '') $errors[] = 'Ingresa tu nombre.';
        if ($rol === '') $errors[] = 'Ingresa tu rol.';
        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ingresa un correo válido.';
        if ($password === '' || strlen($password) < 6) $errors[] = 'La contraseña debe tener mínimo 6 caracteres.';
        if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';

        if (!empty($errors)) {
            $this->render('auth/register', ['errors' => $errors, 'old' => $input]);
            return;
        }

        try {
            $model = new AdministradorModel();
            if ($model->buscarPorCorreo($correo)) {
                $errors[] = 'Ya existe una cuenta con ese correo.';
                $this->render('auth/register', ['errors' => $errors, 'old' => $input]);
                return;
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $id = $model->crearConPassword($nombre, $correo, $rol, $hash);
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_id'] = (int)$id;
            $_SESSION['current_user'] = $model->buscarPorId((int)$id);
            $this->redirect('index.php?controller=Solicitud&action=index');
            return;
        } catch (Throwable $e) {
            $errors[] = 'No se pudo crear la cuenta (revisa la BD).';
            $this->render('auth/register', ['errors' => $errors, 'old' => $input]);
            return;
        }
    }

    private function logout(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('index.php?controller=Auth&action=login');
    }
}
