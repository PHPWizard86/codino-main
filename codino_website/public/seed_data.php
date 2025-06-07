<?php
// public/seed_data.php
// Script to populate initial data for 'plans' and 'ticket_statuses' tables.
// This script is intended to be run once manually.

echo "<pre>"; // For readable output in browser

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/src/Core/Database.php';

use Codino\Core\Database;

try {
    \$db = Database::getInstance()->getConnection();
    \$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ensure errors throw exceptions

    // --- Seed Plans Table ---
    echo "Seeding 'plans' table...\n";
    \$plan_check_stmt = \$db->query("SELECT COUNT(*) as count FROM plans WHERE name = 'Free'");
    \$plan_exists = \$plan_check_stmt->fetchColumn() > 0;

    if (!\$plan_exists) {
        \$plans_data = [
            ['name' => 'Free', 'ticket_limit' => 1, 'price' => 0.00, 'features' => '1 ticket/month'],
            ['name' => 'Standard', 'ticket_limit' => 5, 'price' => 10.00, 'features' => '5 tickets/month + faster response'],
            ['name' => 'Pro', 'ticket_limit' => null, 'price' => 25.00, 'features' => 'Unlimited tickets + priority support'] // null for unlimited
        ];

        \$insert_plan_sql = "INSERT INTO plans (name, ticket_limit, price, features) VALUES (:name, :ticket_limit, :price, :features)";
        \$stmt = \$db->prepare(\$insert_plan_sql);

        foreach (\$plans_data as \$plan) {
            \$stmt->bindParam(':name', \$plan['name']);
            \$stmt->bindParam(':ticket_limit', \$plan['ticket_limit'], \$plan['ticket_limit'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            \$stmt->bindParam(':price', \$plan['price']);
            \$stmt->bindParam(':features', \$plan['features']);
            \$stmt->execute();
            echo "Inserted plan: " . htmlspecialchars(\$plan['name']) . "\n";
        }
        echo "'plans' table seeded successfully.\n";
    } else {
        echo "'Free' plan already exists. Assuming 'plans' table is already seeded. Skipping.\n";
    }

    echo "\n------------------------------------\n\n";

    // --- Seed Ticket Statuses Table ---
    echo "Seeding 'ticket_statuses' table...\n";
    \$status_check_stmt = \$db->query("SELECT COUNT(*) as count FROM ticket_statuses WHERE name = 'Open'");
    \$status_exists = \$status_check_stmt->fetchColumn() > 0;

    if (!\$status_exists) {
        \$statuses_data = [
            ['name' => 'Open'],
            ['name' => 'Under Review'],
            ['name' => 'Resolved'],
            ['name' => 'Needs More Info'],
            ['name' => 'Closed']
        ];

        \$insert_status_sql = "INSERT INTO ticket_statuses (name) VALUES (:name)";
        \$stmt = \$db->prepare(\$insert_status_sql);

        foreach (\$statuses_data as \$status) {
            \$stmt->bindParam(':name', \$status['name']);
            \$stmt->execute();
            echo "Inserted ticket status: " . htmlspecialchars(\$status['name']) . "\n";
        }
        echo "'ticket_statuses' table seeded successfully.\n";
    } else {
        echo "'Open' status already exists. Assuming 'ticket_statuses' table is already seeded. Skipping.\n";
    }

    echo "\nSeeding process complete.\n";

} catch (PDOException \$e) {
    echo "Error during seeding: " . htmlspecialchars(\$e->getMessage()) . "\n";
    error_log("Seeding script PDOException: " . \$e->getMessage());
} catch (Exception \$e) {
    echo "An unexpected error occurred: " . htmlspecialchars(\$e->getMessage()) . "\n";
    error_log("Seeding script Exception: " . \$e->getMessage());
}

echo "</pre>";
?>
