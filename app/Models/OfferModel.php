<?php
namespace App\Models;

use App\Core\Model;

class OfferModel extends Model
{
    protected string $table = 'offers';

    public function getLatest(int $limit = 6): array
    {
        return $this->db->query(
            'SELECT o.*, c.name AS company_name, c.city, c.sector
             FROM offers o
             JOIN companies c ON c.id = o.company_id
             WHERE o.is_active = 1
             ORDER BY o.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function findWithDetails(int $id): array|false
    {
        $offer = $this->db->queryOne(
            'SELECT o.*, c.name AS company_name, c.city, c.sector, c.email AS company_email,
                    c.phone AS company_phone, c.description AS company_desc,
                    (SELECT COUNT(*) FROM applications a WHERE a.offer_id = o.id) AS applicant_count,
                    (SELECT COUNT(*) FROM wishlists w WHERE w.offer_id = o.id) AS wishlist_count
             FROM offers o
             JOIN companies c ON c.id = o.company_id
             WHERE o.id = ?',
            [$id]
        );
        if (!$offer) return false;

        $offer['skills'] = $this->db->query(
            'SELECT s.* FROM skills s JOIN offer_skills os ON os.skill_id = s.id WHERE os.offer_id = ?',
            [$id]
        );
        return $offer;
    }

    public function search(string $search, int $skillId, int $duration, int $page, int $perPage): array
    {
        [$where, $params] = $this->buildSearchClauses($search, $skillId, $duration);
        $sql = 'SELECT o.*, c.name AS company_name, c.city,
                       (SELECT COUNT(*) FROM applications a WHERE a.offer_id = o.id) AS applicant_count
                FROM offers o
                JOIN companies c ON c.id = o.company_id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' ORDER BY o.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = ($page - 1) * $perPage;
        return $this->db->query($sql, $params);
    }

    public function countSearch(string $search, int $skillId, int $duration): int
    {
        [$where, $params] = $this->buildSearchClauses($search, $skillId, $duration);
        $sql = 'SELECT COUNT(*) as cnt FROM offers o JOIN companies c ON c.id = o.company_id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
        return (int)($this->db->queryOne($sql, $params)['cnt'] ?? 0);
    }

    private function buildSearchClauses(string $search, int $skillId, int $duration): array
    {
        $where  = ['o.is_active = 1'];
        $params = [];
        if ($search) {
            $where[]  = '(o.title LIKE ? OR o.description LIKE ? OR c.name LIKE ?)';
            $like     = "%$search%";
            $params   = array_merge($params, [$like, $like, $like]);
        }
        if ($skillId > 0) {
            $where[]  = 'EXISTS (SELECT 1 FROM offer_skills os WHERE os.offer_id = o.id AND os.skill_id = ?)';
            $params[] = $skillId;
        }
        if ($duration > 0) {
            $where[]  = 'o.duration = ?';
            $params[] = $duration;
        }
        return [$where, $params];
    }

    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO offers (company_id,pilot_id,title,description,salary,duration,location,offer_date,is_active)
             VALUES (?,?,?,?,?,?,?,?,?)',
            [$data['company_id'], $data['pilot_id'] ?? null, $data['title'], $data['description'],
             $data['salary'], $data['duration'], $data['location'], $data['offer_date'], $data['is_active']]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->db->execute(
            'UPDATE offers SET company_id=?,title=?,description=?,salary=?,duration=?,location=?,offer_date=?,is_active=?
             WHERE id=?',
            [$data['company_id'], $data['title'], $data['description'], $data['salary'],
             $data['duration'], $data['location'], $data['offer_date'], $data['is_active'], $id]
        );
    }

    public function syncSkills(int $offerId, array $skillIds): void
    {
        $this->db->execute('DELETE FROM offer_skills WHERE offer_id = ?', [$offerId]);
        foreach (array_unique($skillIds) as $sid) {
            if ($sid > 0) {
                $this->db->execute('INSERT IGNORE INTO offer_skills (offer_id,skill_id) VALUES (?,?)', [$offerId, $sid]);
            }
        }
    }

    public function getStats(): array
    {
        return [
            'by_duration'      => $this->db->query('SELECT duration, COUNT(*) AS total FROM offers WHERE is_active=1 GROUP BY duration ORDER BY duration'),
            'top_wishlisted'   => $this->getTopWishlisted(5),
            'total_active'     => $this->count('is_active = 1'),
            'avg_applications' => (float)($this->db->queryOne('SELECT AVG(cnt) as avg FROM (SELECT COUNT(*) as cnt FROM applications GROUP BY offer_id) sub')['avg'] ?? 0),
        ];
    }

    public function getTopWishlisted(int $limit): array
    {
        return $this->db->query(
            'SELECT o.id, o.title, c.name AS company_name, COUNT(w.offer_id) AS wl_count
             FROM offers o
             JOIN companies c ON c.id = o.company_id
             LEFT JOIN wishlists w ON w.offer_id = o.id
             WHERE o.is_active = 1
             GROUP BY o.id
             ORDER BY wl_count DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getStatsByDuration(): array
    {
        return $this->db->query(
            'SELECT duration, COUNT(*) as total FROM offers WHERE is_active=1 GROUP BY duration ORDER BY duration'
        );
    }
}
