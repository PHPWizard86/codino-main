<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/config/config.php';
if (session_status() == PHP_SESSION_NONE) session_start();
\$errors = \$_SESSION['errors'] ?? [];
\$old_input = \$_SESSION['old_input'] ?? [];
unset(\$_SESSION['errors']); unset(\$_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>[ورود کاربر] - <?php echo SITE_NAME; ?></title>
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
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home">[داشبورد]</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">[خروج] (<?php echo htmlspecialchars(\$_SESSION['user_name']); ?>)</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/index.php?route=login">[ورود]</a>
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center">[ورود به حساب کاربری شما]</h2>
                        <?php if (isset(\$_SESSION['flash_message'])): ?>
                            <div class="alert alert-<?php echo htmlspecialchars(\$_SESSION['flash_type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars(\$_SESSION['flash_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="[بستن]"></button>
                            </div>
                            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
                        <?php endif; ?>
                        <?php if (isset(\$errors['credentials'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars(\$errors['credentials'] ?? ''); ?></div>
                        <?php endif; ?>
                        <form action="<?php echo BASE_URL; ?>/login_process.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">[آدرس ایمیل]</label>
                                <input type="email" id="email" name="email" class="form-control <?php echo isset(\$errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars(\$old_input['email'] ?? ''); ?>" required>
                                <?php if (isset(\$errors['email'])): ?><small class="text-danger d-block"><?php echo htmlspecialchars(\$errors['email'] ?? ''); ?></small><?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">[رمز عبور]</label>
                                <input type="password" id="password" name="password" class="form-control <?php echo isset(\$errors['password']) ? 'is-invalid' : ''; ?>" required>
                                <?php if (isset(\$errors['password'])): ?><small class="text-danger d-block"><?php echo htmlspecialchars(\$errors['password'] ?? ''); ?></small><?php endif; ?>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">[ورود]</button>
                            </div>
                        </form>
                        <p class="text-center mt-3">[حساب کاربری ندارید؟] <a href="<?php echo BASE_URL; ?>/index.php?route=register">[اینجا ثبت نام کنید]</a>.</p>
                    </div>
                </div>
            </div>
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
