<?php
/**
 * app/models/Vote.php
 */

class Vote
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function hasVoted(int $issueId, int $userId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM votes WHERE issue_id=? AND user_id=?");
        $stmt->execute([$issueId, $userId]);
        return (bool)$stmt->fetch();
    }

    public function add(int $issueId, int $userId): bool
    {
        try {
            $this->db->prepare("INSERT INTO votes (issue_id, user_id) VALUES (?,?)")
                ->execute([$issueId, $userId]);
            // update denormalised count
            $this->db->prepare("UPDATE issues SET vote_count=vote_count+1 WHERE id=?")
                ->execute([$issueId]);
            return true;
        } catch (PDOException) {
            return false; // duplicate
        }
    }

    public function remove(int $issueId, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM votes WHERE issue_id=? AND user_id=?");
        $stmt->execute([$issueId, $userId]);
        if ($stmt->rowCount() > 0) {
            $this->db->prepare("UPDATE issues SET vote_count=GREATEST(vote_count-1,0) WHERE id=?")
                ->execute([$issueId]);
            return true;
        }
        return false;
    }
}
