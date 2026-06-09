<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>System Diagnostic</h1>";

// Load environment variables
$dbHost = getenv('DB_HOST') ?: 'not set';
$dbPort = getenv('DB_PORT') ?: 'not set';
$dbUser = getenv('DB_USERNAME') ?: 'not set';
$dbPass = getenv('DB_PASSWORD') ?: 'not set';
$dbName = getenv('DB_DATABASE') ?: 'not set';
$redisUrl = getenv('REDIS_URL') ?: 'not set';

echo "<h3>1. Environment</h3>";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'not set') . "<br>";
echo "APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'not set') . "<br>";
echo "LOG_CHANNEL: " . (getenv('LOG_CHANNEL') ?: 'not set') . "<br>";

echo "<h3>2. Database Connection Test</h3>";
try {
    $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span style='color:green'>Database connected successfully!</span><br>";
    
    // Check tables
    $stmt = $pdo->query("SELECT count(*) FROM migrations");
    $count = $stmt->fetchColumn();
    echo "Migrations table exists. Count: $count<br>";
} catch (PDOException $e) {
    echo "<span style='color:red'>Database Connection Failed: " . $e->getMessage() . "</span><br>";
}

echo "<h3>3. Redis Connection Test</h3>";
try {
    if ($redisUrl !== 'not set') {
        $parsed = parse_url($redisUrl);
        $host = $parsed['host'] ?? '127.0.0.1';
        $port = $parsed['port'] ?? 6379;
        
        $redis = new Redis();
        $connected = $redis->connect($host, $port, 2.5); // 2.5 sec timeout
        if ($connected) {
            echo "<span style='color:green'>Redis connected successfully to $host:$port!</span><br>";
        } else {
            echo "<span style='color:red'>Redis connection returned false.</span><br>";
        }
    } else {
        echo "REDIS_URL is not set in environment.<br>";
    }
} catch (Throwable $e) {
    echo "<span style='color:red'>Redis Connection Failed: " . $e->getMessage() . "</span><br>";
}

echo "<h3>4. File Permissions Test</h3>";
$logFile = __DIR__.'/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    echo "laravel.log exists.<br>";
    echo "Writable by www-data? " . (is_writable($logFile) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";
} else {
    echo "laravel.log does not exist yet.<br>";
    $storageDir = __DIR__.'/../storage/logs';
    echo "Storage logs directory writable? " . (is_writable($storageDir) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";
}

echo "<h3>5. Configuration Cache Test</h3>";
$configFile = __DIR__.'/../bootstrap/cache/config.php';
if (file_exists($configFile)) {
    echo "config.php cache exists.<br>";
    echo "Writable by www-data? " . (is_writable($configFile) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";
} else {
    echo "config.php cache does not exist.<br>";
}
