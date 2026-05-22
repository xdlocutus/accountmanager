<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../services/AuditService.php';
require_once __DIR__ . '/../services/JellyfinService.php';
require_once __DIR__ . '/../services/SubscriptionService.php';
require_once __DIR__ . '/../services/UserService.php';

$pdo = db();
$auditService = new AuditService($pdo);
$subscriptionService = new SubscriptionService($pdo, $auditService);
$userService = new UserService($pdo, $subscriptionService, $auditService);
$jellyfinService = new JellyfinService(appConfig()['jellyfin'], $auditService);
