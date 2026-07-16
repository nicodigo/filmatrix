<?php

namespace App\Controllers;

use App\Core\Request;
use Twig\Environment;

class AdminHeroController
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
    private const MIN_WIDTH = 1200;
    private const MIN_HEIGHT = 600;

    private Environment $twig;
    private Request $request;

    public function __construct(Environment $twig, Request $request)
    {
        $this->twig = $twig;
        $this->request = $request;
    }

    /**
     * GET /admin/hero
     * Muestra el formulario de carga con preview de la imagen actual.
     */
    public function index(): void
    {
        echo $this->twig->render('pages/admin/hero.html.twig', [
            'currentImageUrl' => $this->getImageUrl(),
            'flashSuccess'    => $this->request->getFlash('success'),
            'flashError'      => $this->request->getFlash('error'),
        ]);
    }

    /**
     * POST /admin/hero/upload
     * Valida, convierte a WebP y guarda la imagen.
     */
    public function upload(): void
    {
        $file = $this->request->file('hero_image');

        if ($file === null) {
            $this->request->setFlash('error', 'Seleccioná una imagen para subir.');
            header('Location: /admin/hero');
            exit;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->request->setFlash('error', 'Error al subir el archivo (código ' . $file['error'] . ').');
            header('Location: /admin/hero');
            exit;
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            $this->request->setFlash('error', 'La imagen supera el tamaño máximo de 5 MB.');
            header('Location: /admin/hero');
            exit;
        }

        // Validar MIME real con finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_MIMES, true)) {
            $this->request->setFlash('error', 'Formato no permitido. Usá JPEG, PNG o WebP.');
            header('Location: /admin/hero');
            exit;
        }

        // Validar dimensiones mínimas
        $dimensions = getimagesize($file['tmp_name']);
        if ($dimensions === false) {
            $this->request->setFlash('error', 'No se pudieron leer las dimensiones de la imagen.');
            header('Location: /admin/hero');
            exit;
        }

        [$width, $height] = $dimensions;
        if ($width < self::MIN_WIDTH || $height < self::MIN_HEIGHT) {
            $this->request->setFlash('error', sprintf(
                'La imagen debe ser de al menos %d×%d px. Subiste una de %d×%d px.',
                self::MIN_WIDTH,
                self::MIN_HEIGHT,
                $width,
                $height
            ));
            header('Location: /admin/hero');
            exit;
        }

        // Convertir a WebP vía GD
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
            'image/png'  => @imagecreatefrompng($file['tmp_name']),
            'image/webp' => @imagecreatefromwebp($file['tmp_name']),
            default      => false,
        };

        if ($image === false) {
            $this->request->setFlash('error', 'Error al procesar la imagen. Posiblemente el archivo está corrupto.');
            header('Location: /admin/hero');
            exit;
        }

        $uploadDir = __DIR__ . '/../../storage/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/hero-bg.webp';
        $success = imagewebp($image, $destination, 85);
        imagedestroy($image);

        if (!$success) {
            $this->request->setFlash('error', 'Error al guardar la imagen convertida.');
            header('Location: /admin/hero');
            exit;
        }

        $this->request->setFlash('success', 'Imagen del hero actualizada correctamente.');
        header('Location: /admin/hero');
        exit;
    }

    /**
     * POST /admin/hero/reset
     * Elimina la imagen subida y restaura el fallback por defecto.
     */
    public function reset(): void
    {
        $path = __DIR__ . '/../../storage/uploads/hero-bg.webp';
        if (file_exists($path)) {
            unlink($path);
        }

        $this->request->setFlash('success', 'Imagen del hero restaurada a la imagen por defecto.');
        header('Location: /admin/hero');
        exit;
    }

    /**
     * GET /assets/hero-image
     * Sirve la imagen del hero (subida o fallback) con cache-busting.
     * Ruta pública (sin autenticación).
     */
    public function serve(): void
    {
        $path = __DIR__ . '/../../storage/uploads/hero-bg.webp';
        if (!file_exists($path)) {
            header('Location: /assets/img/hero-bg.webp', true, 302);
            exit;
        }

        header('Content-Type: image/webp');
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }

    private function getImageUrl(): string
    {
        $path = __DIR__ . '/../../storage/uploads/hero-bg.webp';
        if (file_exists($path)) {
            $timestamp = filemtime($path);
            return '/assets/hero-image?v=' . $timestamp;
        }
        return '/assets/img/hero-bg.webp';
    }
}
