<?php
/**
 * app/models/User.php
 * All DB operations related to users
 */

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, phone, role)
             VALUES (:name, :email, :password, :phone, :role)"
        );
        $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            ':phone'    => $data['phone'] ?? null,
            ':role'     => $data['role'] ?? 'user',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets   = [];
        $params = [':id' => $id];
        foreach (['name','phone','avatar'] as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "`$field` = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (!$sets) return false;
        $stmt = $this->db->prepare("UPDATE users SET " . implode(',', $sets) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    public function setResetToken(int $id, string $token): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?"
        );
        return $stmt->execute([$token, $id]);
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1"
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function clearResetToken(int $id): void
    {
        $this->db->prepare("UPDATE users SET reset_token=NULL, reset_expires=NULL WHERE id=?")
            ->execute([$id]);
    }

    /** Admin: list all users with pagination */
    public function all(int $limit = 20, int $offset = 0, string $search = ''): array
    {
        $where  = '';
        $params = [];
        if ($search) {
            $where    = "WHERE name LIKE :s OR email LIKE :s";
            $params[':s'] = "%$search%";
        }
        $stmt = $this->db->prepare(
            "SELECT id, name, email, phone, role, is_banned, created_at
             FROM users $where ORDER BY created_at DESC LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(string $search = ''): int
    {
        $where  = '';
        $params = [];
        if ($search) {
            $where    = "WHERE name LIKE :s OR email LIKE :s";
            $params[':s'] = "%$search%";
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users $where");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function setRole(int $id, string $role): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    public function setBan(int $id, bool $ban): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET is_banned = ? WHERE id = ?");
        return $stmt->execute([(int)$ban, $id]);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
