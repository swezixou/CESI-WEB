<?php
namespace App\Models;
use App\Core\Model;

class SkillModel extends Model
{
    protected string $table = 'skills';

    public function all(int $page = 1, int $perPage = 0): array
    {
        return $this->db->query('SELECT * FROM skills ORDER BY label');
    }
}
