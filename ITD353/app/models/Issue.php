<?php
/**
 * app/models/Issue.php
 * All DB operations for issues
 */

class Issue
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    // -------------------------------------------------------
    // Read
    // -------------------------------------------------------

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT i.*, c.name AS category_name, c.icon AS category_icon, c.color AS category_color,
                    u.name AS user_name, u.email AS user_email
             FROM issues i
             JOIN categories c ON c.id = i.category_id
             JOIN users u      ON u.id = i.user_id
             WHERE i.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByTicket(string $ticket): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT i.*, c.name AS category_name, c.icon AS category_icon, c.color AS category_color,
                    u.name AS user_name
             FROM issues i
             JOIN categories c ON c.id = i.category_id
             JOIN users u      ON u.id = i.user_id
             WHERE i.ticket_id = ? LIMIT 1"
        );
        $stmt->execute([$ticket]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Paginated list with filters
     */
    public function list(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        [$where, $params] = $this->buildWhere($filters);
        $order = $this->buildOrder($filters['sort'] ?? 'latest');

        $stmt = $this->db->prepare(
            "SELECT i.*, c.name AS category_name, c.icon AS category_icon, c.color AS category_color,
                    u.name AS user_name
             FROM issues i
             JOIN categories c ON c.id = i.category_id
             JOIN users u      ON u.id = i.user_id
             $where
             ORDER BY $order
             LIMIT :lim OFFSET :off"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        [$where, $params] = $this->buildWhere($filters);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM issues i $where");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function images(int $issueId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM issue_images WHERE issue_id=? ORDER BY sort_order ASC"
        );
        $stmt->execute([$issueId]);
        return $stmt->fetchAll();
    }

    public function statusLogs(int $issueId): array
    {
        $stmt = $this->db->prepare(
            "SELECT l.*, u.name AS changed_by_name
             FROM issue_status_logs l
             LEFT JOIN users u ON u.id = l.changed_by
             WHERE l.issue_id = ?
             ORDER BY l.created_at ASC"
        );
        $stmt->execute([$issueId]);
        return $stmt->fetchAll();
    }

    // -------------------------------------------------------
    // Create / Update
    // -------------------------------------------------------

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO issues
               (ticket_id, user_id, category_id, title, description,
                urgency, status, location_text, latitude, longitude)
             VALUES
               (:ticket_id, :user_id, :category_id, :title, :description,
                :urgency, 'new', :location_text, :latitude, :longitude)"
        );
        // temporary ticket_id – will update after getting ID
        $stmt->execute([
            ':ticket_id'     => 'TMP',
            ':user_id'       => $data['user_id'],
            ':category_id'   => $data['category_id'],
            ':title'         => $data['title'],
            ':description'   => $data['description'],
            ':urgency'       => $data['urgency'],
            ':location_text' => $data['location_text'] ?? null,
            ':latitude'      => $data['latitude'] ?? null,
            ':longitude'     => $data['longitude'] ?? null,
        ]);
        $id = (int)$this->db->lastInsertId();
        $ticket = generateTicketId($id);
        $this->db->prepare("UPDATE issues SET ticket_id=? WHERE id=?")->execute([$ticket, $id]);

        // log status
        $this->logStatus($id, null, null, 'new', 'รายงานปัญหาใหม่');

        return $id;
    }

    public function addImage(int $issueId, string $filename, int $order = 0): void
    {
        $this->db->prepare(
            "INSERT INTO issue_images (issue_id, filename, sort_order) VALUES (?,?,?)"
        )->execute([$issueId, $filename, $order]);
    }

    public function updateStatus(int $id, string $status, ?int $adminId, string $note = ''): bool
    {
        $old = $this->db->prepare("SELECT status FROM issues WHERE id=?");
        $old->execute([$id]);
        $oldStatus = $old->fetchColumn();

        $stmt = $this->db->prepare(
            "UPDATE issues SET status=?, admin_note=CASE WHEN ?!='' THEN ? ELSE admin_note END WHERE id=?"
        );
        $stmt->execute([$status, $note, $note, $id]);

        if ($oldStatus !== $status) {
            $this->logStatus($id, $adminId, $oldStatus, $status, $note);
        }
        return true;
    }

    public function update(int $id, array $data): bool
    {
        $sets   = [];
        $params = [':id' => $id];
        foreach (['title','description','category_id','urgency','location_text','latitude','longitude','is_pinned','admin_note'] as $f) {
            if (array_key_exists($f, $data)) {
                $sets[]       = "`$f`=:$f";
                $params[":$f"] = $data[$f];
            }
        }
        if (!$sets) return false;
        return $this->db->prepare("UPDATE issues SET " . implode(',', $sets) . " WHERE id=:id")
            ->execute($params);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare("DELETE FROM issues WHERE id=?")->execute([$id]);
    }

    // -------------------------------------------------------
    // Dashboard stats
    // -------------------------------------------------------

    public function statsToday(): array
    {
        $stmt = $this->db->query(
            "SELECT status, COUNT(*) AS cnt FROM issues
             WHERE DATE(created_at)=CURDATE() GROUP BY status"
        );
        return $stmt->fetchAll();
    }

    public function statsWeek(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE(created_at) AS day, COUNT(*) AS cnt
             FROM issues
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(created_at)
             ORDER BY day ASC"
        );
        return $stmt->fetchAll();
    }

    public function statsByCategory(): array
    {
        $stmt = $this->db->query(
            "SELECT c.name, c.icon, c.color, COUNT(i.id) AS cnt
             FROM categories c
             LEFT JOIN issues i ON i.category_id=c.id
             GROUP BY c.id ORDER BY cnt DESC LIMIT 8"
        );
        return $stmt->fetchAll();
    }

    public function countByStatus(): array
    {
        $stmt = $this->db->query(
            "SELECT status, COUNT(*) AS cnt FROM issues GROUP BY status"
        );
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['status']] = $row['cnt'];
        }
        return $result;
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    private function buildWhere(array $f): array
    {
        $clauses = [];
        $params  = [];

        if (!empty($f['status'])) {
            $clauses[] = "i.status = :status";
            $params[':status'] = $f['status'];
        }
        if (!empty($f['urgency'])) {
            $clauses[] = "i.urgency = :urgency";
            $params[':urgency'] = $f['urgency'];
        }
        if (!empty($f['category_id'])) {
            $clauses[] = "i.category_id = :cat";
            $params[':cat'] = (int)$f['category_id'];
        }
        if (!empty($f['search'])) {
            $clauses[] = "(i.title LIKE :q1 OR i.description LIKE :q2 OR i.ticket_id LIKE :q3)";
            $params[':q1'] = '%' . $f['search'] . '%';
            $params[':q2'] = '%' . $f['search'] . '%';
            $params[':q3'] = '%' . $f['search'] . '%';
        }
        if (!empty($f['date_from'])) {
            $clauses[] = "i.created_at >= :df";
            $params[':df'] = $f['date_from'] . ' 00:00:00';
        }
        if (!empty($f['date_to'])) {
            $clauses[] = "i.created_at <= :dt";
            $params[':dt'] = $f['date_to'] . ' 23:59:59';
        }
        if (!empty($f['user_id'])) {
            $clauses[] = "i.user_id = :uid";
            $params[':uid'] = (int)$f['user_id'];
        }

        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }

    private function buildOrder(string $sort): string
    {
        return match($sort) {
            'votes'    => 'i.is_pinned DESC, i.vote_count DESC, i.created_at DESC',
            'oldest'   => 'i.is_pinned DESC, i.created_at ASC',
            default    => 'i.is_pinned DESC, i.created_at DESC',
        };
    }

    private function logStatus(int $issueId, ?int $userId, ?string $old, string $new, string $note): void
    {
        $this->db->prepare(
            "INSERT INTO issue_status_logs (issue_id, changed_by, old_status, new_status, note)
             VALUES (?,?,?,?,?)"
        )->execute([$issueId, $userId, $old, $new, $note]);
    }
}
