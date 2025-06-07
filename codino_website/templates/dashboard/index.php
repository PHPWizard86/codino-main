<?php
// templates/dashboard/index.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}
require_once BASE_PATH . '/config/config.php'; // For SITE_NAME, BASE_URL

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset(\$_SESSION['user_id'])) {
    \$_SESSION['flash_message'] = '[لطفا برای دسترسی به داشبورد خود وارد شوید.]'; // Localized this message
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

\$user_name = htmlspecialchars(\$_SESSION['user_name'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>[داشبورد] - <?php echo SITE_NAME; ?></title>
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
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">[خروج] (<?php echo \$user_name; ?>)</a>
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
        <h2>[به داشبورد خود خوش آمدید،] <?php echo \$user_name; ?>!</h2>

        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars(\$_SESSION['flash_type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars(\$_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="[بستن]"></button>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <p>[این مرکز مدیریت حساب کاربری، وب سایت ها و تیکت های پشتیبانی شماست.]</p>

        <nav class="dashboard-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_profile">[مشاهده/ویرایش پروفایل]</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_websites">[مدیریت وب سایت های من]</a></li>
                <li><a href="#">[مدیریت تیکت های پشتیبانی (به زودی)]</a></li>
            </ul>
        </nav>
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
