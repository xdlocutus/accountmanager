<?php

declare(strict_types=1);

class AuditService
{
    public function __construct(private PDO $db)
    {
    }

    public function log(?int $userId, string $action, array $details = []): void
    {
        $stmt = $this->db->prepare('INSERT INTO audit_logs (user_id, action, details, created_at) VALUES (:user_id, :action, :details, NOW())');
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':details' => json_encode($details, JSON_UNESCAPED_SLASHES),
        ]);
    }
}
