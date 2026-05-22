<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$subscriptionService->lifecycleSweep();
echo "expiry check complete\n";
