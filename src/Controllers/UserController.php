<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\UserService;

class UserController
{
    private AuthService $authService;
    private UserService $userService;
    public string $viewsDir;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function perfil()
    {
        $userId = $this->authService->getCurrentUserId();
        $usuario = $this->userService->getUserById($userId);

        require $this->viewsDir . 'pages/miperfil.php';
    }

    public function login()
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /perfil');
            exit;
        }

        $error = '';

        require $this->viewsDir . 'pages/login.php';
    }

    public function hacerLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Completá todos los campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido.';
        } else {
            if ($this->authService->login($email, $password)) {
                $destino = $_SESSION['redirect_after_login'] ?? '/perfil';
                unset($_SESSION['redirect_after_login']);

                header('Location: ' . $destino);
                exit;
            } else {
                $error = 'Email o contraseña incorrectos.';
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

    public function registro()
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /perfil');
            exit;
        }

        $error = '';
        $campos = [
            'nombre' => '',
            'email' => ''
        ];

        require $this->viewsDir . 'pages/registro.php';
    }

    public function hacerRegistro()
    {
        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        $error = '';
        $campos = [
            'nombre' => $nombre,
            'email'  => $email
        ];

        if (empty($nombre) || empty($email) || empty($password) || empty($confirm)) {
            $error = 'Completá todos los campos.';
        } elseif (strlen($nombre) < 2) {
            $error = 'El nombre debe tener al menos 2 caracteres.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido.';
        } elseif (strlen($password) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif ($password !== $confirm) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            $registered = $this->authService->register([
                'username' => $nombre,
                'email' => $email,
                'password' => $password,
            ]);

            if ($registered) {
                header('Location: /perfil');
                exit;
            } else {
                $error = 'El email ya está registrado.';
            }
        }

        require $this->viewsDir . 'pages/registro.php';
    }

    public function editarPerfil()
    {
        $userId = $this->authService->getCurrentUserId();
        $usuario = $this->userService->getUserById($userId);

        $error = '';
        $success = '';

        require $this->viewsDir . 'pages/editar_perfil.php';
    }

    public function guardarPerfil()
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
        } elseif ($this->userService->emailExists($email, $userId)) {
            $error = 'El email ya está en uso.';
        } elseif (!empty($password_nueva) && strlen($password_nueva) < 8) {
            $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } elseif (!empty($password_nueva) && $password_nueva !== $password_confirm) {
            $error = 'Las contraseñas no coinciden.';
        } elseif (!empty($password_nueva) && !$usuario->verifyPassword($password_actual)) {
            $error = 'La contraseña actual es incorrecta.';
        } else {

            if (!empty($password_nueva)) {
                $hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                $this->userService->updateUserWithPassword($userId, $nombre, $email, $hash);
            } else {
                $this->userService->updateUser($userId, $nombre, $email);
            }

            $_SESSION['user_nombre'] = $nombre;
            $success = 'Perfil actualizado correctamente.';

            $usuario = $this->userService->getUserById($userId);
        }

        require $this->viewsDir . 'pages/editar_perfil.php';
    }
}
