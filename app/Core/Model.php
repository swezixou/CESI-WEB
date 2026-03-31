<?php
namespace App\Core;

/**
 * Modèle de base – accès PDO mutualisé + helpers CRUD.
 */
abstract class Model
{
    protected Database $db;
    protected string   $table  = '';
    protected string   $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Retourne tous les enregistrements (avec pagination optionnelle) */
    public function all(int $page = 1, int $perPage = 0): array
    {
        if ($perPage > 0) {
            $offset = ($page - 1) * $perPage;
            return $this->db->query(
                "SELECT * FROM `{$this->table}` LIMIT :limit OFFSET :offset",
                [':limit' => $perPage, ':offset' => $offset]
            );
        }
        return $this->db->query("SELECT * FROM `{$this->table}`");
    }

    /** Trouve un enregistrement par son ID */
    public function find(int $id): array|false
    {
        return $this->db->queryOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /** Compte les enregistrements */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM `{$this->table}`";
        if ($where) $sql .= " WHERE $where";
        $row = $this->db->queryOne($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }

    /** Supprime un enregistrement */
    public function delete(int $id): int
    {
        return $this->db->execute(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /** Échappe les sorties HTML */
    public static function e(mixed $val): string
    {
        return htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
