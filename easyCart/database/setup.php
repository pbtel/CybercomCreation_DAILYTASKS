<?php
/**
 * Database Setup Script - EasyCart Phase 6
 * Creates database and runs schema using PHP PDO
 */

echo "========================================\n";
echo "EasyCart Database Setup (PHP)\n";
echo "========================================\n\n";

// Database configuration
$host = 'localhost';
$port = '5432';
$user = 'postgres';
$pass = 'root';
$dbname = 'easycart_db';

try {
    // Step 1: Connect to PostgreSQL (without database)
    echo "Step 1: Connecting to PostgreSQL...\n";
    $dsn = "pgsql:host=$host;port=$port";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✓ Connected to PostgreSQL\n\n";
    
    // Step 2: Drop and create database
    echo "Step 2: Creating database '$dbname'...\n";
    
    // Check if database exists
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '$dbname'");
    if ($stmt->fetch()) {
        echo "  Database exists, dropping it...\n";
        // Terminate existing connections
        $pdo->exec("SELECT pg_terminate_backend(pg_stat_activity.pid)
                    FROM pg_stat_activity
                    WHERE pg_stat_activity.datname = '$dbname'
                    AND pid <> pg_backend_pid()");
        $pdo->exec("DROP DATABASE $dbname");
        echo "  ✓ Database dropped\n";
    }
    
    $pdo->exec("CREATE DATABASE $dbname");
    echo "✓ Database '$dbname' created successfully!\n\n";
    
    // Step 3: Connect to the new database
    echo "Step 3: Connecting to database '$dbname'...\n";
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✓ Connected to database\n\n";
    
    // Step 4: Run schema
    echo "Step 4: Running schema.sql...\n";
    $schemaFile = __DIR__ . '/schema.sql';
    
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }
    
    $schema = file_get_contents($schemaFile);
    
    // Execute schema
    $pdo->exec($schema);
    echo "✓ Schema executed successfully!\n\n";
    
    // Step 5: Verify tables
    echo "Step 5: Verifying tables...\n";
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables 
                         WHERE table_schema = 'public' 
                         ORDER BY table_name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "  Created " . count($tables) . " tables:\n";
    foreach ($tables as $table) {
        echo "    ✓ $table\n";
    }
    
    echo "\n========================================\n";
    echo "✓ Database setup completed successfully!\n";
    echo "========================================\n\n";
    echo "Next step: Run migrate.php to import existing data\n";
    echo "Command: php migrate.php\n\n";
    
} catch (PDOException $e) {
    echo "\n❌ ERROR: Database setup failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "  - PostgreSQL is running\n";
    echo "  - Credentials are correct (user: $user, password: $pass)\n";
    echo "  - PostgreSQL is accessible on $host:$port\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
