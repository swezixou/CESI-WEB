<?php
namespace App\Models;

use App\Core\Model;

class CompanyModel extends Model
{
    protected string $table = 'companies';

    public function search(string $search, string $sector, int $page, int $perPage): array
    {
        [$where, $params] = $this->buildClauses($search, $sector);
        $sql = 'SELECT c.*,
                       ROUND(AVG(r.rating),1) AS avg_rating,
                       COUNT(DISTINCT r.id) AS review_count,
                       COUNT(DISTINCT a.id) AS applicant_count
                FROM companies c
                LEFT JOIN company_reviews r ON r.company_id = c.id
                LEFT JOIN offers o ON o.company_id = c.id
                LEFT JOIN applications a ON a.offer_id = o.id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' GROUP BY c.id ORDER BY c.name LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = ($page - 1) * $perPage;
        return $this->db->query($sql, $params);
    }

    public function countSearch(string $search, string $sector): int
    {
        [$where, $params] = $this->buildClauses($search, $sector);
        $sql = 'SELECT COUNT(*) as cnt FROM companies c' . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
        return (int)($this->db->queryOne($sql, $params)['cnt'] ?? 0);
    }

    private function buildClauses(string $search, string $sector): array
    {
        $where  = ['c.is_active = 1'];
        $params = [];
        if ($search) {
            $where[]  = '(c.name LIKE ? OR c.description LIKE ?)';
            $like     = "%$search%";
            $params   = array_merge($params, [$like, $like]);
        }
        if ($sector) {
            $where[]  = 'c.sector = ?';
            $params[] = $sector;
        }
        return [$where, $params];
    }

    public function findWithStats(int $id): array|false
    {
        return $this->db->queryOne(
            'SELECT c.*, ROUND(AVG(r.rating),1) AS avg_rating,
                    COUNT(DISTINCT r.id) AS review_count,
                    COUNT(DISTINCT a.id) AS applicant_count
             FROM companies c
             LEFT JOIN company_reviews r ON r.company_id = c.id
             LEFT JOIN offers o ON o.company_id = c.id
             LEFT JOIN applications a ON a.offer_id = o.id
             WHERE c.id = ?
             GROUP BY c.id',
            [$id]
        );
    }

    public function getOffers(int $id): array
    {
        return $this->db->query(
            'SELECT o.*, (SELECT COUNT(*) FROM applications a WHERE a.offer_id = o.id) AS applicant_count
             FROM offers o WHERE o.company_id = ? AND o.is_active = 1 ORDER BY o.created_at DESC',
            [$id]
        );
    }

    public function getReviews(int $id): array
    {
        return $this->db->query(
            'SELECT r.*, u.firstname, u.lastname
             FROM company_reviews r
             JOIN students st ON st.id = r.student_id
             JOIN users u ON u.id = st.user_id
             WHERE r.company_id = ? ORDER BY r.created_at DESC',
            [$id]
        );
    }

    public function hasReview(int $companyId, int $studentId): bool
    {
        return (bool)$this->db->queryOne(
            'SELECT id FROM company_reviews WHERE company_id = ? AND student_id = ?',
            [$companyId, $studentId]
        );
    }

    public function addReview(int $companyId, int $studentId, int $rating, string $comment): void
    {
        $this->db->execute(
            'INSERT INTO company_reviews (company_id,student_id,rating,comment) VALUES (?,?,?,?)
             ON DUPLICATE KEY UPDATE rating=VALUES(rating), comment=VALUES(comment)',
            [$companyId, $studentId, $rating, $comment]
        );
    }

    // Évaluation par admin/pilote (utilise student_id=1 par défaut ou crée un review sans student)
    public function addReviewByUser(int $companyId, int $userId, int $rating, string $comment): void
    {
        // Cherche si cet user est aussi un étudiant
        $student = $this->db->queryOne('SELECT id FROM students WHERE user_id = ?', [$userId]);
        $studentId = $student ? $student['id'] : null;

        if ($studentId) {
            $this->db->execute(
                'INSERT INTO company_reviews (company_id,student_id,rating,comment) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE rating=VALUES(rating), comment=VALUES(comment)',
                [$companyId, $studentId, $rating, $comment]
            );
        } else {
            // Admin/pilote : on stocke directement (student_id nullable dans une version future)
            // Pour l'instant on skip silencieusement si pas d'étudiant lié
            // Note: la table requiert student_id NOT NULL, donc on informe
        }
    }

    public function getSectors(): array
    {
        return $this->db->query('SELECT DISTINCT sector FROM companies WHERE sector IS NOT NULL ORDER BY sector');
    }

    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO companies (name,description,email,phone,city,sector,is_active) VALUES (?,?,?,?,?,?,?)',
            [$data['name'],$data['description'],$data['email'],$data['phone'],$data['city'],$data['sector'],$data['is_active']]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->db->execute(
            'UPDATE companies SET name=?,description=?,email=?,phone=?,city=?,sector=? WHERE id=?',
            [$data['name'],$data['description'],$data['email'],$data['phone'],$data['city'],$data['sector'],$id]
        );
    }
}
