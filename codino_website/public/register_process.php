<?php
// public/register_process.php

// Define BASE_PATH if not already defined (e.g., if accessed directly)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/src/Core/Database.php'; // For database interaction

use Codino\Core\Database; // Namespace for Database class

// Start session for flash messages and redirect
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure this script is accessed via POST
if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
    \$_SESSION['flash_message'] = 'Invalid request method.';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=register');
    exit;
}

// --- Input Validation ---
\$name = trim(\$_POST['name'] ?? '');
\$email = trim(\$_POST['email'] ?? '');
\$phone_number = trim(\$_POST['phone_number'] ?? '');
\$password = \$_POST['password'] ?? '';
\$confirm_password = \$_POST['confirm_password'] ?? '';

\$errors = [];
\$old_input = [
    'name' => \$name,
    'email' => \$email,
    'phone_number' => \$phone_number
];

// Name (optional, no specific validation for now beyond trim)

// Email validation
if (empty(\$email)) {
    \$errors['email'] = 'Email is required.';
} elseif (!filter_var(\$email, FILTER_VALIDATE_EMAIL)) {
    \$errors['email'] = 'Invalid email format.';
}

// Phone number (optional, basic validation if provided)
if (!empty(\$phone_number)) {
    if (!preg_match('/^[0-9+()-]{7,20}\$/', \$phone_number)) { // Basic regex for phone chars
        \$errors['phone_number'] = 'Invalid characters in phone number or invalid length.';
    }
}

// Password validation
if (empty(\$password)) {
    \$errors['password'] = 'Password is required.';
} elseif (strlen(\$password) < 8) {
    \$errors['password'] = 'Password must be at least 8 characters long.';
}

// Confirm password validation
if (empty(\$confirm_password)) {
    \$errors['confirm_password'] = 'Please confirm your password.';
} elseif (\$password !== \$confirm_password) {
    \$errors['confirm_password'] = 'Passwords do not match.';
}

if (!empty(\$errors)) {
    \$_SESSION['errors'] = \$errors;
    \$_SESSION['old_input'] = \$old_input;
    header('Location: ' . BASE_URL . '/index.php?route=register');
    exit;
}

// --- Database Interaction ---
try {
    \$db = Database::getInstance()->getConnection();

    // Check if email already exists
    \$stmt = \$db->prepare("SELECT id FROM users WHERE email = :email");
    \$stmt->bindParam(':email', \$email);
    \$stmt->execute();
    if (\$stmt->fetch()) {
        \$errors['email'] = 'This email address is already registered.';
    }

    // Check if phone number already exists (if provided)
    if (!empty(\$phone_number)) {
        \$stmt = \$db->prepare("SELECT id FROM users WHERE phone_number = :phone_number");
        \$stmt->bindParam(':phone_number', \$phone_number);
        \$stmt->execute();
        if (\$stmt->fetch()) {
            \$errors['phone_number'] = 'This phone number is already registered.';
        }
    }

    if (!empty(\$errors)) {
        \$_SESSION['errors'] = \$errors;
        \$_SESSION['old_input'] = \$old_input;
        header('Location: ' . BASE_URL . '/index.php?route=register');
        exit;
    }

    // Hash the password
    \$password_hash = password_hash(\$password, PASSWORD_DEFAULT);

    // Get default plan ID (e.g., 'Free' plan)
    // For now, assume 'Free' plan has ID 1 or query it.
    // This part relies on the 'plans' table being populated (Step 6 of overall plan)
    \$default_plan_id = null;
    \$plan_stmt = \$db->prepare("SELECT id FROM plans WHERE name = 'Free' LIMIT 1");
    \$plan_stmt->execute();
    \$free_plan = \$plan_stmt->fetch();
    if (\$free_plan) {
        \$default_plan_id = \$free_plan['id'];
    } else {
        // Fallback or error if Free plan isn't found.
        // For now, we'll allow registration without a plan if 'Free' plan is missing,
        // but ideally, this should be handled robustly (e.g., prevent registration or log error).
        // Or, we could create the Free plan here if it doesn't exist, but that's more complex for this script.
        // For now, plan_id will be NULL if 'Free' plan is not found.
        // error_log("Default 'Free' plan not found during registration for user: " . \$email);
    }

    // Insert user into database
    \$sql = "INSERT INTO users (name, email, phone_number, password_hash, plan_id) VALUES (:name, :email, :phone_number, :password_hash, :plan_id)";
    \$stmt = \$db->prepare(\$sql);

    \$stmt->bindParam(':name', \$name, PDO::PARAM_STR);
    \$stmt->bindParam(':email', \$email, PDO::PARAM_STR);

    // Bind phone_number only if it's not empty, otherwise NULL
    if (!empty(\$phone_number)) {
        \$stmt->bindParam(':phone_number', \$phone_number, PDO::PARAM_STR);
    } else {
        \$null_phone = null;
        \$stmt->bindParam(':phone_number', \$null_phone, PDO::PARAM_NULL);
    }

    \$stmt->bindParam(':password_hash', \$password_hash, PDO::PARAM_STR);
    \$stmt->bindParam(':plan_id', \$default_plan_id, PDO::PARAM_INT); // This can be NULL if plan not found

    \$stmt->execute();

    // Set success message and redirect to login page (or homepage with message)
    \$_SESSION['flash_message'] = 'Registration successful! Please log in.';
    \$_SESSION['flash_type'] = 'success';
    // We'll create the login route and page in the next step.
    // For now, redirecting to registration page with success message.
    header('Location: ' . BASE_URL . '/index.php?route=register');
    // header('Location: ' . BASE_URL . '/index.php?route=login'); // Ideal redirect after login page is made
    exit;

} catch (PDOException \$e) {
    error_log("Database error during registration: " . \$e->getMessage());
    \$_SESSION['errors'] = ['database' => 'A database error occurred. Please try again later.'];
    \$_SESSION['old_input'] = \$old_input; // Preserve input
    header('Location: ' . BASE_URL . '/index.php?route=register');
    exit;
} catch (Exception \$e) {
    error_log("General error during registration: " . \$e->getMessage());
    \$_SESSION['errors'] = ['general' => 'An unexpected error occurred. Please try again.'];
    \$_SESSION['old_input'] = \$old_input; // Preserve input
    header('Location: ' . BASE_URL . '/index.php?route=register');
    exit;
}

?>
