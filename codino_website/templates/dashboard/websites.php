<?php
// templates/dashboard/websites.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/src/Core/Database.php'; // To fetch websites

use Codino\Core\Database;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset(\$_SESSION['user_id'])) {
    \$_SESSION['flash_message'] = '[لطفا برای مدیریت وب سایت های خود وارد شوید.]';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

\$user_id = \$_SESSION['user_id'];
\$user_name = htmlspecialchars(\$_SESSION['user_name'] ?? 'User');

// Fetch user's registered websites
\$user_websites = [];
try {
    \$db = Database::getInstance()->getConnection();
    \$stmt = \$db->prepare("SELECT id, domain_name, website_type, description, DATE_FORMAT(registration_date, '%Y-%m-%d') as formatted_reg_date FROM websites WHERE user_id = :user_id ORDER BY registration_date DESC");
    \$stmt->bindParam(':user_id', \$user_id, PDO::PARAM_INT);
    \$stmt->execute();
    \$user_websites = \$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception \$e) {
    error_log("Error fetching user websites: " . \$e->getMessage());
    \$_SESSION['flash_message'] = '[به دلیل خطای پایگاه داده، بازیابی وب سایت های شما امکان پذیر نبود.]';
    \$_SESSION['flash_type'] = 'danger';
}

// For the form
\$form_errors = \$_SESSION['form_errors']['website_registration'] ?? [];
\$form_old_input = \$_SESSION['form_old_input']['website_registration'] ?? [];
unset(\$_SESSION['form_errors']['website_registration']);
unset(\$_SESSION['form_old_input']['website_registration']);

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>[مدیریت وب سایت ها] - <?php echo SITE_NAME; ?></title>
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
        <h2>[مدیریت وب سایت های شما]</h2>

        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars(\$_SESSION['flash_type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars(\$_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="[بستن]"></button>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title text-center">[ثبت وب سایت جدید]</h3>
                <form action="<?php echo BASE_URL; ?>/website_process.php" method="POST">
                    <div class="mb-3">
                        <label for="domain_name" class="form-label">[نام دامنه (مثال: example.com)]</label>
                        <input type="text" id="domain_name" name="domain_name" class="form-control <?php echo isset(\$form_errors['domain_name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars(\$form_old_input['domain_name'] ?? ''); ?>" required>
                        <?php if (isset(\$form_errors['domain_name'])): ?><small class="text-danger d-block"><?php echo htmlspecialchars(\$form_errors['domain_name'] ?? ''); ?></small><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="website_type" class="form-label">[نوع وب سایت (مثال: وبلاگ، فروشگاه، شرکتی)]</label>
                        <input type="text" id="website_type" name="website_type" class="form-control" value="<?php echo htmlspecialchars(\$form_old_input['website_type'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">[توضیحات یا یادداشت ها]</label>
                        <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars(\$form_old_input['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">[ثبت وب سایت]</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="websites-list">
            <h3>[وب سایت های ثبت شده شما]</h3>
            <?php if (!empty(\$user_websites)): ?>
                <table class="table table-striped table-hover mt-4">
                    <thead>
                        <tr>
                            <th>[نام دامنه]</th>
                            <th>[نوع]</th>
                            <th>[توضیحات]</th>
                            <th>[تاریخ ثبت]</th>
                            {# <th>Actions</th> #}
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (\$user_websites as \$website): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(\$website['domain_name']); ?></td>
                                <td><?php echo htmlspecialchars(\$website['website_type'] ?: 'N/A'); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(\$website['description'] ?: 'N/A')); ?></td>
                                <td><?php echo htmlspecialchars(\$website['formatted_reg_date']); ?></td>
                                {# <td><a href="#">Edit</a> | <a href="#" onclick="return confirm('Are you sure?')">Delete</a></td> #}
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="alert alert-info">[شما هنوز هیچ وب سایتی ثبت نکرده اید.]</p>
            <?php endif; ?>
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
