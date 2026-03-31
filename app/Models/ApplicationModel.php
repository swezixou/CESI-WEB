<?php
namespace App\Models;
use App\Core\Model;

class ApplicationModel extends Model
{
    protected string $table = 'applications';

    public function exists(int $studentId, int $offerId): bool
    {
        return (bool)$this->db->queryOne(
            'SELECT id FROM applications WHERE student_id=? AND offer_id=?', [$studentId, $offerId]
        );
    }

    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO applications (student_id,offer_id,cover_letter,cv_path) VALUES (?,?,?,?)',
            [$data['student_id'], $data['offer_id'], $data['cover_letter'], $data['cv_path'] ?? null]
        );
        return (int)$this->db->lastInsertId();
    }

    public function getByStudent(int $studentId): array
    {
        return $this->db->query(
            'SELECT a.*,o.title,o.salary,c.name AS company_name
             FROM applications a
             JOIN offers o ON o.id=a.offer_id
             JOIN companies c ON c.id=o.company_id
             WHERE a.student_id=? ORDER BY a.applied_at DESC',
            [$studentId]
        );
    }

    public function getByPilot(int $pilotId): array
    {
        return $this->db->query(
            'SELECT a.*,o.title,c.name AS company_name,
                    u.firstname,u.lastname,u.email
             FROM applications a
             JOIN offers o ON o.id=a.offer_id
             JOIN companies c ON c.id=o.company_id
             JOIN students s ON s.id=a.student_id
             JOIN users u ON u.id=s.user_id
             WHERE s.pilot_id=? ORDER BY a.applied_at DESC',
            [$pilotId]
        );
    }

    public function getRecent(int $limit): array
    {
        return $this->db->query(
            'SELECT a.*,o.title,c.name AS company_name,u.firstname,u.lastname
             FROM applications a
             JOIN offers o ON o.id=a.offer_id
             JOIN companies c ON c.id=o.company_id
             JOIN students s ON s.id=a.student_id
             JOIN users u ON u.id=s.user_id
             ORDER BY a.applied_at DESC LIMIT ?',
            [$limit]
        );
    }

    public function updateStatus(int $id, string $status): void
    {
        $this->db->execute('UPDATE applications SET status=? WHERE id=?', [$status, $id]);
    }
}
