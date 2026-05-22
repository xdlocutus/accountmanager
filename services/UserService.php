<?php

declare(strict_types=1);

class UserService
{
    public function __construct(private PDO $db, private SubscriptionService $subscriptionService, private AuditService $audit)
    {
    }

    public function register(string $email, string $password, int $packageId): int
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            throw new InvalidArgumentException('Invalid registration data');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO users (email, password_hash, role, status, created_at) VALUES (:email, :password_hash, :role, :status, NOW())');
        $stmt->execute([':email' => strtolower($email), ':password_hash' => $hash, ':role' => 'user', ':status' => 'active']);
        $userId = (int) $this->db->lastInsertId();
        $this->subscriptionService->assignPackage($userId, $packageId);
        $this->subscriptionService->enqueueJob('create_user', ['user_id' => $userId, 'email' => $email, 'password' => $password]);
        $this->audit->log($userId, 'user_registered');
        return $userId;
    }

    public function authenticate(string $email, string $password): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1');
        $stmt->execute([':email' => strtolower($email)]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        return $user;
    }
}
