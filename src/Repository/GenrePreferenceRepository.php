<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

/**
 * Acceso a datos de la tabla user_genre_preferences.
 *
 * Almacena el peso (0.0 – 1.0) que cada género tiene para un usuario,
 * derivado de su comportamiento: películas vistas, descartadas y reseñadas.
 */
class GenrePreferenceRepository
{
    public function __construct(private PDO $db) {}

    /**
     * Retorna un mapa genre_id => weight para el usuario.
     *
     * @return array<int, float>
     */
    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT genre_id, weight
             FROM user_genre_preferences
             WHERE user_id = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[(int) $row['genre_id']] = (float) $row['weight'];
        }

        return $result;
    }    

    /**
     * Incrementa o decrementa el peso de un género para un usuario.
     *
     * Crea la fila si no existe. Clampea el resultado en [0.0, 1.0].
     *
     * @param float $delta Valor positivo para incrementar, negativo para decrementar.
     */
    public function adjustWeight(int $userId, int $genreId, float $delta): void
    {
        // Al insertar: si delta > 0 partimos de delta, si delta < 0 queda en 0.
        $initial = max(0.0, min(1.0, $delta));

        $stmt = $this->db->prepare(
            'INSERT INTO user_genre_preferences (user_id, genre_id, weight, updated_at)
             VALUES (:user_id, :genre_id, :initial, NOW())
             ON CONFLICT (user_id, genre_id)
             DO UPDATE SET
                 weight     = GREATEST(0.0, LEAST(1.0, user_genre_preferences.weight + :delta)),
                 updated_at = NOW()'
        );

        $stmt->execute([
            'user_id'  => $userId,
            'genre_id' => $genreId,
            'initial'  => $initial,
            'delta'    => $delta,
        ]);
    }
}
