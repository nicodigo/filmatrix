<?php
/**
 * UserRepository
 * Acceso a datos de la tabla users.
 *
 * MÉTODOS:
 *   findByEmail(email): ?User
 *     Busca un usuario por su email. Retorna null si no existe.
 *
 *   findById(id): ?User
 *     Busca un usuario por su id interno. Retorna null si no existe.
 *
 *   findByUsername(username): ?User
 *     Busca un usuario por su username. Retorna null si no existe.
 *
 *   save(user): int
 *     Inserta un nuevo usuario y retorna el id generado.
 *
 *   update(user): bool
 *     Actualiza username, email, password_hash y timestamp de un usuario
 *     existente. Retorna true si la operación fue exitosa.
 * 
 *   getStatsByUserId(userId): array
 *     Calcula estadísticas generales de actividad de un usuario
 *     basadas en sus reseñas.
 *     Retorna un array asociativo con estadísticas resumidas
 *     del perfil del usuario.
 * 
 *    getPublicReviewsByUserId(userId): array
 *     Retorna todas las reseñas públicas realizadas por un usuario,
 *     incluyendo información resumida de los títulos asociados.
 *     Las reseñas se ordenan por fecha de creación descendente
 *     y solo se incluyen aquellas marcadas como visibles.
 *
 * DEPENDENCIAS:
 *   PDO  — conexión a la base de datos.
 *   User — modelo mapeado desde los resultados de la consulta.
 */

namespace App\Repository;

use PDO;
use App\Models\User;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );

        $stmt->execute([
            ':email' => $email
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return User::fromArray($row);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE id = :id LIMIT 1'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return User::fromArray($row);
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE username = :username LIMIT 1'
        );

        $stmt->execute([
            ':username' => $username
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return User::fromArray($row);
    }

    public function save(User $user): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash, role, created_at, updated_at)
             VALUES (:username, :email, :password_hash, :role, NOW(), NOW())'
        );

        $stmt->execute([
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':password_hash' => $user->getPasswordHash(),
            ':role' => $user->getRole(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET username = :username,
                 email = :email,
                 password_hash = :password_hash,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':password_hash' => $user->getPasswordHash(),
            ':id' => $user->getId()
        ]);
    }

    public function getStatsByUserId(int $userId): array
    {
        // Total de reseñas
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) as total_reviews
            FROM reviews
            WHERE user_id = :user_id'
        );
        $stmt->execute([':user_id' => $userId]);
        $totalReviews = (int) $stmt->fetchColumn();

        // Horas totales (suma de duration_minutes de títulos reseñados)
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(t.duration_minutes), 0) as total_minutes
            FROM reviews r
            JOIN titles t ON t.id = r.title_id
            WHERE r.user_id = :user_id
            AND t.duration_minutes IS NOT NULL'
        );
        $stmt->execute([':user_id' => $userId]);
        $totalMinutes = (int) $stmt->fetchColumn();

        // Géneros más vistos (top 3)
        $stmt = $this->pdo->prepare(
            'SELECT g.name, ugp.weight AS cnt
             FROM user_genre_preferences ugp
             JOIN genres g ON g.id = ugp.genre_id
             WHERE ugp.user_id = :user_id
             ORDER BY ugp.weight DESC
             LIMIT 3'
        );
        $stmt->execute([':user_id' => $userId]);
        $topGenres = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        return [
            'total_reviews' => $totalReviews,
            'total_hours'   => (int) floor($totalMinutes / 60),
            'top_genres'    => $topGenres,
        ];
    }

    public function getPublicReviewsByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                r.id,
                r.score,
                r.body,
                r.created_at,
                t.title,
                t.poster_url,
                t.tmdb_id
            FROM reviews r
            JOIN titles t ON t.id = r.title_id
            WHERE r.user_id = :user_id
            AND r.is_visible = true
            ORDER BY r.created_at DESC'
        );

        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
