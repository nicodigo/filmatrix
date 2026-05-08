<?php

namespace App\Controllers;

use App\Core\Exceptions\EmailAlreadyTakenException;
use App\Core\Exceptions\InvalidPasswordException;
use App\Core\Exceptions\UserNotFoundException;
use App\Core\Exceptions\UsernameAlreadyExistsException;
use App\Services\AuthService;
use App\Services\UserService;

class UserController
{
    private AuthService $authService;
    private UserService $userService;
    private string $viewsDir;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function profile()
    {
        $userId = $this->authService->getCurrentUserId();
        $usuario = $this->userService->getUserById($userId);

        require $this->viewsDir . 'pages/miperfil.php';
    }

    public function login()
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /profile');
            exit;
        }

        $error = '';

        require $this->viewsDir . 'pages/login.php';
    }

    public function handleLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Completá todos los campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido.';
        } else {
            try {
                $this->authService->login($email, $password);
                $destino = $_SESSION['redirect_after_login'] ?? '/profile';
                unset($_SESSION['redirect_after_login']);

                header('Location: ' . $destino);
                exit;
            } catch (UserNotFoundException $e) {
                $error = $e->getMessage();
            } catch (InvalidPasswordException $e) {
                $error = $e->getMessage();
            }
        }

        require $this->viewsDir . 'pages/login.php';
    }

    public function logout()
    {
        $this->authService->logout();
        header('Location: /login');
        exit;
    }

    public function register()
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /profile');
            exit;
        }

        $error = '';
        $campos = [
            'nombre' => '',
            'email' => ''
        ];

        require $this->viewsDir . 'pages/registro.php';
    }

    public function handleRegister()
    {
        $username = mb_strtolower(trim($_POST['username'] ?? ''), 'UTF8');
        $email    = mb_strtolower(trim($_POST['email'] ?? ''), 'UTF-8');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';


        $campos = [
            'nombre' => $username,
            'email'  => $email
        ];

        $error = $this->verifyRegisterFields($username, $email, $password, $confirm);
        if ($error) {
            require $this->viewsDir . 'pages/registro.php';
            exit;
        }

        try {
            $this->authService->register([
                'username' => $nombre,
                'email' => $email,
                'password' => $password,
            ]);
        } catch (UsernameAlreadyExistsException $e) {
            $error = $e->getMessage();
        } catch (EmailAlreadyTakenException $e) {
            $error = $e->getMessage();
        } finally {
            if ($error) {
                require $this->viewsDir . 'pages/registro.php';
            } else {
                require $this->viewsDir . 'pages/login.php';
            }
        }
    }

    private function verifyRegisterFields(
        string $username,
        string $email,
        string $password,
        string $confirm
    ): ?string {
        if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
            $error = 'Completá todos los campos.';
        } elseif (strlen($username) < 2) {
            $error = 'El nombre debe tener al menos 2 caracteres.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido.';
        } elseif (strlen($password) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif ($password !== $confirm) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            return null;
        }

        return $error;
    }

    public function editProfile()
    {
        $userId = $this->authService->getCurrentUserId();
        $usuario = $this->userService->getUserById($userId);

        $error = '';
        $success = '';

        require $this->viewsDir . 'pages/editar_perfil.php';
    }

    public function updateProfile()
    {
        $userId = $this->authService->getCurrentUserId();
        $usuario = $this->userService->getUserById($userId);

        $nombre           = trim($_POST['nombre'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $password_actual  = $_POST['password_actual'] ?? '';
        $password_nueva   = $_POST['password_nueva'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        $error = '';
        $success = '';

        if (empty($nombre) || empty($email)) {
            $error = 'El nombre y el email son obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido.';
        } elseif (!empty($password_nueva) && strlen($password_nueva) < 8) {
            $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } elseif (!empty($password_nueva) && $password_nueva !== $password_confirm) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            try {
                $this->userService->assertEmailNotTaken($email, $userId);
                if (!empty($password_nueva)) {
                    $this->userService->updateProfileWithPassword(
                        $userId,
                        $nombre,
                        $email,
                        $password_actual,
                        $password_nueva
                    );
                } else {
                    $this->userService->updateProfile($userId, $nombre, $email);
                }

                $_SESSION['username'] = $nombre;
                $success = 'Perfil actualizado correctamente.';
                $usuario = $this->userService->getUserById($userId);
            } catch (EmailAlreadyTakenException $e) {
                $error = $e->getMessage();
            } catch (InvalidPasswordException $e) {
                $error = $e->getMessage();
            } catch (UserNotFoundException $e) {
                $error = $e->getMessage();
            }
        }

        require $this->viewsDir . 'pages/editar_perfil.php';
    }
}
