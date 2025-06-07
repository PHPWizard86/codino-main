<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title><?php echo SITE_NAME; ?> - [صفحه اصلی]</title>
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
                            <a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/index.php?route=home">[خانه]</a>
                        </li>
                        <?php if (isset(\$_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home">[داشبورد]</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">[خروج] (<?php echo htmlspecialchars(\$_SESSION['user_name']); ?>)</a>
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
        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars(\$_SESSION['flash_type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars(\$_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="[بستن]"></button>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5 text-center">
                <h1 class="display-5 fw-bold">[به کدینو خوش آمدید]</h1>
                <p class="fs-4 col-md-10 mx-auto">[ارائه دهنده تخصصی پشتیبانی فنی دستی و شخصی سازی شده برای وب سایت شما.]</p>
                <?php if (!isset(\$_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=register" class="btn btn-primary btn-lg mt-3">[ثبت نام کنید]</a>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=login" class="btn btn-secondary btn-lg mt-3">[ورود به حساب کاربری]</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home" class="btn btn-success btn-lg mt-3">[رفتن به داشبورد]</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row align-items-md-stretch text-center">
            <div class="col-md-12">
                <h2 class="pb-2 border-bottom mb-4">[چگونه کار می کنیم؟]</h2>
            </div>
            <div class="col-md-4">
                <div class="h-100 p-4 text-bg-dark rounded-3">
                    <h3>[۱. ثبت نام و تعریف وب سایت]</h3>
                    <p>[حساب کاربری خود را ایجاد کرده و وب سایتی که نیاز به پشتیبانی دارد را ثبت کنید.]</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="h-100 p-4 bg-light border rounded-3">
                    <h3>[۲. ارسال تیکت پشتیبانی]</h3>
                    <p>[مشکل فنی خود را با جزئیات کامل از طریق سیستم تیکتینگ ما ارسال کنید.]</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="h-100 p-4 text-bg-dark rounded-3">
                    <h3>[۳. بررسی دستی توسط متخصص]</h3>
                    <p>[کارشناسان ما شخصا مشکل شما را بررسی و تشخیص می دهند - بدون ربات یا اسکن خودکار.]</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5">
             <p class="lead">[ما همه چیز را از باگ های ظاهری و لینک های خراب گرفته تا مشکلات سئو و بهینه سازی سرعت، با دقت کامل انجام می دهیم.]</p>
             <a href="<?php echo BASE_URL; ?>/index.php?route=plans" class="btn btn-outline-primary btn-lg">[مشاهده پلن های اشتراک]</a>
        </div>
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
