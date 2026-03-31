<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): array|false
    {
        return $this->db->queryOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public function emailExists(string $email): bool
    {
        return (bool)$this->db->queryOne('SELECT id FROM users WHERE email = ?', [$email]);
    }

    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO users (firstname,lastname,email,password,role) VALUES (?,?,?,?,?)',
            [$data['firstname'], $data['lastname'], $data['email'], $data['password'], $data['role']]
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
        $this->db->execute('UPDATE users SET ' . implode(',', $sets) . ' WHERE id = ?', $params);
    }

    public function search(string $search, string $role, int $page, int $perPage): array
    {
        $where  = [];
        $params = [];
        if ($search) {
            $where[]  = '(firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)';
            $like     = "%$search%";
            $params   = array_merge($params, [$like, $like, $like]);
        }
        if ($role) {
            $where[]  = 'role = ?';
            $params[] = $role;
        }
        $sql    = 'SELECT * FROM users' . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
        $sql   .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $params = array_merge($params, [$perPage, ($page - 1) * $perPage]);
        return $this->db->query($sql, $params);
    }

    public function countSearch(string $search, string $role): int
    {
        $where  = [];
        $params = [];
        if ($search) {
            $where[]  = '(firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)';
            $like     = "%$search%";
            $params   = array_merge($params, [$like, $like, $like]);
        }
        if ($role) {
            $where[]  = 'role = ?';
            $params[] = $role;
        }
        $sql = 'SELECT COUNT(*) as cnt FROM users' . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
        return (int)($this->db->queryOne($sql, $params)['cnt'] ?? 0);
    }

    public function find(int $id): array|false
    {
        return $this->db->queryOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->execute('DELETE FROM users WHERE id = ?', [$id]);
    }

}
