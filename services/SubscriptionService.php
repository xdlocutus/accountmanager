<?php

declare(strict_types=1);

class SubscriptionService
{
    public function __construct(private PDO $db, private AuditService $audit)
    {
    }

    public function assignPackage(int $userId, int $packageId): void
    {
        $pkg = $this->getPackage($packageId);
        $stmt = $this->db->prepare('UPDATE users SET package_id = :package_id, expires_at = DATE_ADD(NOW(), INTERVAL :days DAY), status = :status WHERE id = :id');
        $stmt->bindValue(':package_id', $packageId, PDO::PARAM_INT);
        $stmt->bindValue(':days', (int) $pkg['duration_days'], PDO::PARAM_INT);
        $stmt->bindValue(':status', 'active');
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $this->audit->log($userId, 'subscription_assigned', ['package_id' => $packageId]);
    }

    public function lifecycleSweep(): void
    {
        $toGrace = $this->db->query("SELECT id FROM users WHERE expires_at < NOW() AND status = 'active'")->fetchAll();
        foreach ($toGrace as $row) {
            $this->db->prepare("UPDATE users SET status='grace' WHERE id=:id")->execute([':id' => $row['id']]);
            $this->audit->log((int) $row['id'], 'subscription_grace_started');
        }

        $toExpire = $this->db->query("SELECT id, jellyfin_user_id FROM users WHERE expires_at < DATE_SUB(NOW(), INTERVAL 3 DAY) AND status IN ('grace','expired')")->fetchAll();
        foreach ($toExpire as $row) {
            $this->db->prepare("UPDATE users SET status='expired' WHERE id=:id")->execute([':id' => $row['id']]);
            $this->enqueueJob('disable_user', ['user_id' => $row['id'], 'jellyfin_user_id' => $row['jellyfin_user_id']]);
        }
    }

    public function enqueueJob(string $type, array $payload): void
    {
        $stmt = $this->db->prepare('INSERT INTO job_queue (type, payload, status, retry_count, created_at) VALUES (:type, :payload, :status, 0, NOW())');
        $stmt->execute([':type' => $type, ':payload' => json_encode($payload), ':status' => 'pending']);
    }

    public function packages(): array { return $this->db->query('SELECT * FROM packages ORDER BY price ASC')->fetchAll(); }
    public function addPackage(string $name, float $price, int $days, string $description): void {
        $this->db->prepare('INSERT INTO packages (name,price,duration_days,description) VALUES (:n,:p,:d,:x)')->execute([':n'=>$name,':p'=>$price,':d'=>$days,':x'=>$description]);
        $this->audit->log(null,'package_created',['name'=>$name,'days'=>$days]);
    }
    public function extendUser(int $userId, int $days): void {
        $this->db->prepare('UPDATE users SET expires_at=DATE_ADD(COALESCE(expires_at,NOW()), INTERVAL :d DAY), status=:s WHERE id=:id')->execute([':d'=>$days,':id'=>$userId,':s'=>'active']);
        $this->audit->log($userId,'subscription_extended',['days'=>$days]);
    }

    public function confirmPayment(int $userId): void
    {
        $stmt = $this->db->prepare('SELECT package_id FROM users WHERE id=:id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();
        if (!$user || empty($user['package_id'])) {
            throw new InvalidArgumentException('User or package not found');
        }
        $this->assignPackage($userId, (int) $user['package_id']);
        $this->audit->log($userId, 'payment_confirmed');
    }

    private function getPackage(int $id): array
    {
        $stmt = $this->db->prepare('SELECT * FROM packages WHERE id=:id');
        $stmt->execute([':id' => $id]);
        $pkg = $stmt->fetch();
        if (!$pkg) {
            throw new InvalidArgumentException('Package not found');
        }
        return $pkg;
    }
}
