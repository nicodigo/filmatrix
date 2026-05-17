<?php

/**
 * UserController
 * Maneja la autenticación y gestión de perfil de los usuarios.
 *
 * MÉTODOS:
 *   profile(): Renderiza el perfil del usuario autenticado.
 *
 *   login(): Renderiza el formulario de login. Redirige a /profile si ya hay sesión activa.
 * 
 *   handleLogin(): Procesa el formulario de login. Valida los campos, autentica al usuario
 *                  y redirige al destino guardado en sesión (o a /profile por defecto).
 *
 *   logout(): Cierra la sesión del usuario y redirige a /login.
 *
 *   register(): Renderiza el formulario de registro. Redirige a /profile si ya hay sesión activa.
 *
 *   handleRegister(): Procesa el formulario de registro. Valida los campos con verifyRegisterFields(),
 *     registra al usuario y redirige al login. En caso de error muestra el formulario.
 *
 *   editProfile(): Renderiza el formulario de edición de perfil con los datos actuales del usuario.
 *
 *   updateProfile(): Procesa el formulario de edición. Valida campos, actualiza nombre y email,
 *                    y opcionalmente la contraseña si se provee una nueva.
 *
 *
 * DEPENDENCIAS:
 *   AuthService  — autenticación, registro, sesión.
 *   UserService  — consulta y actualización de datos del usuario.
 */

namespace App\Controllers;

use App\Core\Exceptions\EmailAlreadyTakenException;
use App\Core\Exceptions\InvalidPasswordException;
use App\Core\Exceptions\UserNotFoundException;
use App\Core\Exceptions\UsernameAlreadyExistsException;
use App\Core\Request;
use App\Services\AuthService;
use App\Services\UserService;
use Twig\Environment;

class UserController
{
    private Environment $twig;
    private AuthService $authService;
    private UserService $userService;
    private Request $request;

    public function __construct(Environment $twig, AuthService $authService, UserService $userService, Request $request)
    {
        $this->twig = $twig;
        $this->authService = $authService;
        $this->userService = $userService;
        $this->request = $request;
    }

    public function profile()
    {
        $userId = $this->authService->getCurrentUserId();
        $user = $this->userService->getUserById($userId);

        echo $this->twig->render('pages/profile.html.twig', [
            'user' => $user,
        ]);
    }

    public function login()
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /profile');
            exit;
        }

        $error = '';

        echo $this->twig->render('pages/login.html.twig', [
            'error' => $error,
            'emailValue' => '',
        ]);
    }

    public function handleLogin()
    {
        $email = trim($this->request->post('email', ''));
        $password = $this->request->post('password', '');
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Completá todos los campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido.';
        } else {
            try {
                $this->authService->login($email, $password);
                $destination = $this->request->session('redirect_after_login', '/profile');
                $this->request->unsetSession('redirect_after_login');

                header('Location: ' . $destination);
                exit;
            } catch (UserNotFoundException $e) {
                $error = $e->getMessage();
            } catch (InvalidPasswordException $e) {
                $error = $e->getMessage();
            }
        }

        echo $this->twig->render('pages/login.html.twig', [
            'error' => $error,
            'emailValue' => $email,
        ]);
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
        $fields = [
            'username' => '',
            'email' => ''
        ];

        echo $this->twig->render('pages/register.html.twig', [
            'error' => $error,
            'fields' => $fields,
        ]);
    }

    public function handleRegister()
    {
        $username = mb_strtolower(trim($this->request->post('username', '')), 'UTF-8');
        $email    = mb_strtolower(trim($this->request->post('email', '')), 'UTF-8');
        $password = $this->request->post('password', '');
        $confirm  = $this->request->post('confirm_password', '');


        $fields = [
            'username' => $username,
            'email'  => $email
        ];

        $error = $this->verifyRegisterFields($username, $email, $password, $confirm);
        if ($error) {
            echo $this->twig->render('pages/register.html.twig', [
                'error' => $error,
                'fields' => $fields,
            ]);
            exit;
        }

        try {
            $this->authService->register([
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ]);
        } catch (UsernameAlreadyExistsException $e) {
            $error = $e->getMessage();
        } catch (EmailAlreadyTakenException $e) {
            $error = $e->getMessage();
        } finally {
            if ($error) {
                echo $this->twig->render('pages/register.html.twig', [
                    'error' => $error,
                    'fields' => $fields,
                ]);
            } else {
                header('Location: /login');
                exit;
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
        $user = $this->userService->getUserById($userId);

        $error = '';
        $success = '';

        echo $this->twig->render('pages/edit-profile.html.twig', [
            'user'    => $user,
            'error'   => $error,
            'success' => $success,
        ]);
    }

    public function updateProfile()
    {
        $userId = $this->authService->getCurrentUserId();
        $user = $this->userService->getUserById($userId);

        $username           = trim($this->request->post('username', ''));
        $email            = trim($this->request->post('email', ''));
        $currentPassword  = $this->request->post('current_password', '');
        $newPassword   = $this->request->post('new_password', '');
        $confirmPassword = $this->request->post('confirm_password', '');

        $error = '';
        $success = '';

        if (empty($username) || empty($email)) {
            $error = 'El nombre y el email son obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido.';
        } elseif (!empty($newPassword) && strlen($newPassword) < 8) {
            $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } elseif (!empty($newPassword) && $newPassword !== $confirmPassword) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            try {
                $this->userService->assertEmailNotTaken($email, $userId);
                if (!empty($newPassword)) {
                    $this->userService->updateProfileWithPassword(
                        $userId,
                        $username,
                        $email,
                        $currentPassword,
                        $newPassword
                    );
                } else {
                    $this->userService->updateProfile($userId, $username, $email);
                }

                $this->request->setSession('username', $username);
                $success = 'Perfil actualizado correctamente.';
                $user = $this->userService->getUserById($userId);
            } catch (EmailAlreadyTakenException $e) {
                $error = $e->getMessage();
            } catch (InvalidPasswordException $e) {
                $error = $e->getMessage();
            } catch (UserNotFoundException $e) {
                $error = $e->getMessage();
            }
        }

        echo $this->twig->render('pages/edit-profile.html.twig', [
            'user'    => $user,
            'error'   => $error,
            'success' => $success,
        ]);
    }
}
