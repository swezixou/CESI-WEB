<?php
namespace App\Models;

use App\Core\Model;

class PilotModel extends Model
{
    protected string $table = 'pilots';

    public function findByUserId(int $userId): array|false
    {
        return $this->db->queryOne('SELECT * FROM pilots WHERE user_id = ?', [$userId]);
    }

    public function findWithUser(int $id): array|false
    {
        return $this->db->queryOne(
            'SELECT pi.*,u.firstname,u.lastname,u.email,u.is_active,
                    (SELECT COUNT(*) FROM students s WHERE s.pilot_id=pi.id) AS student_count
             FROM pilots pi JOIN users u ON u.id=pi.user_id WHERE pi.id=?',
            [$id]
        );
    }

    public function getAllWithUser(): array
    {
        return $this->db->query(
            'SELECT pi.*,u.firstname,u.lastname FROM pilots pi JOIN users u ON u.id=pi.user_id ORDER BY u.lastname'
        );
    }

    public function search(string $search, int $page, int $perPage): array
    {
        $where  = [];
        $params = [];
        if ($search) {
            $where[]  = '(u.firstname LIKE ? OR u.lastname LIKE ?)';
            $like     = "%$search%";
            $params   = array_merge($params, [$like, $like]);
        }
        $sql = 'SELECT pi.*,u.firstname,u.lastname,u.email,
                       (SELECT COUNT(*) FROM students s WHERE s.pilot_id=pi.id) AS student_count
                FROM pilots pi JOIN users u ON u.id=pi.user_id'
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
            $where[]  = '(u.firstname LIKE ? OR u.lastname LIKE ?)';
            $like     = "%$search%";
            $params   = array_merge($params, [$like, $like]);
        }
        $sql = 'SELECT COUNT(*) as cnt FROM pilots pi JOIN users u ON u.id=pi.user_id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
        return (int)($this->db->queryOne($sql, $params)['cnt'] ?? 0);
    }

    public function create(array $data): int
    {
        $this->db->execute('INSERT INTO pilots (user_id,promotion) VALUES (?,?)', [$data['user_id'], $data['promotion'] ?? '']);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->db->execute('UPDATE pilots SET promotion=? WHERE id=?', [$data['promotion'], $id]);
    }
}
