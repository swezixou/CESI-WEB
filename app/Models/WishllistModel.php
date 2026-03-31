<?php
namespace App\Models;
use App\Core\Model;

class WishlistModel extends Model
{
    protected string $table = 'wishlists';

    public function exists(int $studentId, int $offerId): bool
    {
        return (bool)$this->db->queryOne(
            'SELECT 1 FROM wishlists WHERE student_id=? AND offer_id=?', [$studentId, $offerId]
        );
    }

    public function add(int $studentId, int $offerId): void
    {
        $this->db->execute(
            'INSERT IGNORE INTO wishlists (student_id,offer_id) VALUES (?,?)', [$studentId, $offerId]
        );
    }

    public function remove(int $studentId, int $offerId): void
    {
        $this->db->execute(
            'DELETE FROM wishlists WHERE student_id=? AND offer_id=?', [$studentId, $offerId]
        );
    }

    public function getByStudent(int $studentId): array
    {
        return $this->db->query(
            'SELECT o.*,c.name AS company_name,c.city
             FROM wishlists w
             JOIN offers o ON o.id=w.offer_id
             JOIN companies c ON c.id=o.company_id
             WHERE w.student_id=? AND o.is_active=1
             ORDER BY w.added_at DESC',
            [$studentId]
        );
    }
}
