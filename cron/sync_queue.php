<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$maxRetries = appConfig()['queue_max_retries'];
$jobs = $pdo->query("SELECT * FROM job_queue WHERE status IN ('pending','failed') AND retry_count < {$maxRetries} ORDER BY id ASC LIMIT 50")->fetchAll();
foreach ($jobs as $job) {
    try {
        $payload = json_decode($job['payload'], true, 512, JSON_THROW_ON_ERROR);
        if ($job['type'] === 'create_user') {
            $result = $jellyfinService->createUser($payload['email'], $payload['password']);
            $pdo->prepare('UPDATE users SET jellyfin_user_id=:jid WHERE id=:id')->execute([':jid'=>$result['id'],':id'=>$payload['user_id']]);
        } elseif ($job['type'] === 'disable_user') {
            $jellyfinService->disableUser((string) $payload['jellyfin_user_id']);
        } elseif ($job['type'] === 'enable_user') {
            $jellyfinService->enableUser((string) $payload['jellyfin_user_id']);
        } elseif ($job['type'] === 'update_policy') {
            $jellyfinService->updateUserPolicy((string) $payload['jellyfin_user_id'], $payload['policy']);
        }
        $pdo->prepare("UPDATE job_queue SET status='success' WHERE id=:id")->execute([':id'=>$job['id']]);
        $auditService->log($payload['user_id'] ?? null, 'job_success', ['job_id' => $job['id'], 'type' => $job['type']]);
    } catch (Throwable $e) {
        $pdo->prepare("UPDATE job_queue SET status='failed', retry_count=retry_count+1 WHERE id=:id")->execute([':id'=>$job['id']]);
        $auditService->log(null, 'job_failure', ['job_id'=>$job['id'], 'error'=>$e->getMessage()]);
    }
}
echo "sync queue complete\n";
