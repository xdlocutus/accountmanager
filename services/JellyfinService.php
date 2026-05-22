<?php

declare(strict_types=1);

class JellyfinService
{
    public function __construct(private array $config, private AuditService $audit)
    {
    }

    public function createUser(string $username, string $password): array
    {
        $payload = ['Name' => $username, 'Password' => $password];
        $templateUserId = $this->findUserIdByName($this->config['template_user'] ?? '');
        if ($templateUserId !== null) {
            $payload['CopyFromUserId'] = $templateUserId;
        }

        $response = $this->request('POST', '/Users/New', $payload);
        return ['id' => $response['Id'] ?? null, 'raw' => $response];
    }

    public function disableUser(string $jellyfinUserId): array
    {
        return $this->updateUserPolicy($jellyfinUserId, ['IsDisabled' => true, 'EnableContentDeletion' => false]);
    }

    public function enableUser(string $jellyfinUserId): array
    {
        return $this->updateUserPolicy($jellyfinUserId, ['IsDisabled' => false]);
    }

    public function updateUserPolicy(string $jellyfinUserId, array $policy): array
    {
        $result = $this->request('POST', '/Users/' . rawurlencode($jellyfinUserId) . '/Policy', $policy);
        $this->audit->log(null, 'jellyfin_policy_update', ['jellyfin_user_id' => $jellyfinUserId, 'policy' => $policy]);
        return $result;
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $url = $this->config['url'] . $path;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Emby-Token: ' . $this->config['api_key'],
            ],
            CURLOPT_TIMEOUT => 20,
        ]);
        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resp === false || $code >= 400) {
            $error = curl_error($ch) ?: ('HTTP ' . $code);
            curl_close($ch);
            throw new RuntimeException('Jellyfin API error: ' . $error);
        }
        curl_close($ch);
        return json_decode((string) $resp, true) ?? [];
    }

    private function findUserIdByName(string $username): ?string
    {
        $username = trim($username);
        if ($username === '') {
            return null;
        }

        $users = $this->request('GET', '/Users');
        foreach ($users as $user) {
            if (!is_array($user)) {
                continue;
            }
            if (strcasecmp((string) ($user['Name'] ?? ''), $username) === 0) {
                $id = (string) ($user['Id'] ?? '');
                return $id !== '' ? $id : null;
            }
        }

        return null;
    }
}
