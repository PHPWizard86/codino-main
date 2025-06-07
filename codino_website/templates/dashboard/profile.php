<?php
// templates/dashboard/profile.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}
require_once BASE_PATH . '/config/config.php'; // For SITE_NAME, BASE_URL
require_once BASE_PATH . '/src/Core/Database.php'; // To fetch fresh user data if needed

use Codino\Core\Database;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset(\$_SESSION['user_id'])) {
    \$_SESSION['flash_message'] = '[لطفا برای دسترسی به پروفایل خود وارد شوید.]';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

// Fetch user data from DB for up-to-date info (optional, session data can also be used)
\$user = null;
try {
    \$db = Database::getInstance()->getConnection();
    \$stmt = \$db->prepare("SELECT u.name, u.email, u.phone_number, p.name as plan_name
                           FROM users u
                           LEFT JOIN plans p ON u.plan_id = p.id
                           WHERE u.id = :user_id");
    \$stmt->bindParam(':user_id', \$_SESSION['user_id'], PDO::PARAM_INT);
    \$stmt->execute();
    \$user = \$stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception \$e) {
    error_log("Error fetching user profile data: " . \$e->getMessage());
    // Handle error, maybe redirect or show a message
}

if (!\$user) {
    // This case should ideally not happen if user_id in session is valid
    \$_SESSION['flash_message'] = '[بازیابی اطلاعات پروفایل شما امکان پذیر نبود.]';
    \$_SESSION['flash_type'] = 'danger';
    // Potentially log out user or redirect to dashboard home
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_home');
    exit;
}

\$user_name_display = htmlspecialchars(\$user['name'] ?? 'N/A');
\$user_email_display = htmlspecialchars(\$user['email'] ?? 'N/A');
\$user_phone_display = htmlspecialchars(\$user['phone_number'] ?? 'N/A');
\$user_plan_display = htmlspecialchars(\$user['plan_name'] ?? 'No active plan');

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>[پروفایل کاربر] - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php?route=home"><?php echo SITE_NAME; ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="[تغییر وضعیت ناوبری]">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=home">[خانه]</a>
                        </li>
                        <?php if (isset(\$_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home">[داشبورد]</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">[خروج] (<?php echo \$user_name_display; ?>)</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=login">[ورود]</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=register">[ثبت نام]</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-4">
        <h2>[اطلاعات پروفایل شما]</h2>

        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars(\$_SESSION['flash_type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars(\$_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="[بستن]"></button>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="profile-details">
            <p><strong>[نام:]</strong> <?php echo \$user_name_display; ?></p>
            <p><strong>[ایمیل:]</strong> <?php echo \$user_email_display; ?></p>
            <p><strong>[شماره تلفن:]</strong> <?php echo \$user_phone_display; ?></p>
            <p><strong>[پلن اشتراک:]</strong> <?php echo \$user_plan_display; ?></p>
        </div>

        {# Placeholder for edit profile form/link #}
        <button class="btn btn-info edit-profile-btn">[ویرایش پروفایل]</button>
        <button class="btn btn-warning edit-profile-btn">[تغییر رمز عبور]</button>

    </main>

    <footer class="bg-dark text-white text-center p-4 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?> - [تمامی حقوق محفوظ است.]</p>
            <p class="mb-0">
                <a href="<?php echo BASE_URL; ?>/index.php?route=terms" class="text-white-50">[شرایط استفاده از خدمات]</a> |
                <a href="<?php echo BASE_URL; ?>/index.php?route=contact" class="text-white-50">[تماس با ما]</a>
            </p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
