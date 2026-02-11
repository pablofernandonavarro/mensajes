<?php

$token = $_GET['token'] ?? '';

if ($token !== getenv('DEPLOY_TOKEN')) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$output = [];
$commands = [
    'composer2 install --no-dev --optimize-autoloader --ignore-platform-reqs 2>&1',
    'php artisan config:cache 2>&1',
    'php artisan route:cache 2>&1',
    'php artisan view:cache 2>&1',
    'php artisan migrate --force 2>&1',
];

foreach ($commands as $command) {
    exec($command, $output, $returnCode);
    if ($returnCode !== 0) {
        http_response_code(500);
        echo implode("\n", $output);
        exit;
    }
}

echo "Deploy OK\n";
echo implode("\n", $output);
