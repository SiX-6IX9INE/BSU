<?php
/**
 * app/models/Category.php
 */

class Category
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order ASC"
        );
        return $stmt->fetchAll();
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO categories (name, slug, icon, color, sort_order)
             VALUES (:name, :slug, :icon, :color, :sort_order)"
        );
        $stmt->execute([
            ':name'       => $data['name'],
            ':slug'       => $data['slug'],
            ':icon'       => $data['icon'] ?? '📌',
            ':color'      => $data['color'] ?? '#3b82f6',
            ':sort_order' => $data['sort_order'] ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE categories SET name=:name, slug=:slug, icon=:icon,
             color=:color, sort_order=:sort_order, is_active=:is_active WHERE id=:id"
        );
        return $stmt->execute([
            ':name'       => $data['name'],
            ':slug'       => $data['slug'],
            ':icon'       => $data['icon'] ?? '📌',
            ':color'      => $data['color'] ?? '#3b82f6',
            ':sort_order' => $data['sort_order'] ?? 0,
            ':is_active'  => $data['is_active'] ?? 1,
            ':id'         => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        // ตรวจว่า category ถูกใช้อยู่
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM issues WHERE category_id=?");
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) return false;

        return $this->db->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    }

    public function slugExists(string $slug, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categories WHERE slug=? AND id!=?");
        $stmt->execute([$slug, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
