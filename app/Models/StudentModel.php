<?php
namespace App\Models;

use App\Core\Model;

class StudentModel extends Model
{
    protected string $table = 'students';

    public function findByUserId(int $userId): array|false
    {
        return $this->db->queryOne(
            'SELECT * FROM students WHERE user_id = ?',
            [$userId]
        );
    }

    /**
     * Récupère un étudiant avec les infos user et la promotion du pilote.
     * Bug corrigé : alias SQL cohérents avec la vue.
     */
    public function findWithUser(int $id): array|false
    {
        return $this->db->queryOne(
            'SELECT s.*,
                    u.firstname, u.lastname, u.email, u.is_active,
                    u.created_at AS joined_at,
                    pu.firstname AS pilot_firstname,
                    pu.lastname  AS pilot_lastname,
                    pi.promotion
             FROM students s
             JOIN  users u  ON u.id  = s.user_id
             LEFT JOIN pilots pi ON pi.id = s.pilot_id
             LEFT JOIN users pu  ON pu.id = pi.user_id
             WHERE s.id = ?',
            [$id]
        );
    }

    /**
     * Recherche paginée des étudiants avec infos user + promotion.
     * Bug corrigé : alias promotion cohérent (pi.promotion via users pi).
     */
/**
 * Recherche paginée des étudiants avec infos user + promotion + pilote.
 */
    public function search(string $search, int $page, int $perPage): array
        {
    $where  = [];
    $params = [];

    if ($search) {
        $where[]  = '(u.firstname LIKE ? OR u.lastname LIKE ? OR u.email LIKE ?)';
        $like     = "%$search%";
        $params   = array_merge($params, [$like, $like, $like]);
    }

    $sql = 'SELECT s.*,
                   u.firstname, u.lastname, u.email, u.is_active,
                   p.promotion,
                   pu.firstname AS pilot_firstname,
                   pu.lastname AS pilot_lastname
            FROM students s
            JOIN  users u   ON u.id  = s.user_id
            LEFT JOIN pilots p  ON p.id  = s.pilot_id
            LEFT JOIN users pu  ON pu.id = p.user_id'
         . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
         . ' ORDER BY u.lastname LIMIT ? OFFSET ?';

    $params[] = $perPage;
    $params[] = ($page - 1) * $perPage;

    return $this->db->query($sql, $params);
}

    public function countSearch(string $search): int
    {
        $where  = [];
        $params = [];

        if ($search) {
            $where[]  = '(u.firstname LIKE ? OR u.lastname LIKE ? OR u.email LIKE ?)';
            $like     = "%$search%";
            $params   = array_merge($params, [$like, $like, $like]);
        }

        $sql = 'SELECT COUNT(*) as cnt FROM students s JOIN users u ON u.id = s.user_id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '');

        return (int)($this->db->queryOne($sql, $params)['cnt'] ?? 0);
    }

    public function getByPilot(int $pilotId): array
    {
        return $this->db->query(
            'SELECT s.*, u.firstname, u.lastname, u.email
             FROM students s
             JOIN users u ON u.id = s.user_id
             WHERE s.pilot_id = ?',
            [$pilotId]
        );
    }

    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO students (user_id, pilot_id, stage_status) VALUES (?, ?, ?)',
            [
                $data['user_id'],
                $data['pilot_id'] ?? null,
                $data['stage_status'] ?? 'searching',
            ]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sets   = [];
        $params = [];
        foreach ($data as $col => $val) {
            $sets[]   = "`$col` = ?";
            $params[] = $val;
        }
        $params[] = $id;
        $this->db->execute(
            'UPDATE students SET ' . implode(', ', $sets) . ' WHERE id = ?',
            $params
        );
    }
}
