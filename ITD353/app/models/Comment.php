<?php
/**
 * app/models/Comment.php
 */

class Comment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function byIssue(int $issueId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.name AS user_name, u.role AS user_role
             FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.issue_id = ?
             ORDER BY c.is_pinned DESC, c.created_at ASC"
        );
        $stmt->execute([$issueId]);
        return $stmt->fetchAll();
    }

    public function create(int $issueId, int $userId, string $body): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO comments (issue_id, user_id, body) VALUES (?,?,?)"
        );
        $stmt->execute([$issueId, $userId, $body]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id, int $userId, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            return $this->db->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
        }
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id=? AND user_id=?");
        $stmt->execute([$id, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function togglePin(int $id): bool
    {
        return $this->db->prepare("UPDATE comments SET is_pinned = NOT is_pinned WHERE id=?")
            ->execute([$id]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
