<?php

session_start();

require_once 'config/db.php';
require_once 'Notifications_functions.php';
require_once 'Notifications_repository.php';

const DEFAULT_STUDENT_ID = 'SV001';
const ALLOWED_AUDIENCES = ['SINH_VIEN', 'GIANG_VIEN', 'TAT_CA'];

function emptyNotificationForm(): array
{
    return [
        'id' => 0,
        'title' => '',
        'description' => '',
        'image_url' => '',
        'doi_tuong' => 'SINH_VIEN',
    ];
}

function formFromPost(int $notificationId): array
{
    return [
        'id' => $notificationId,
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'doi_tuong' => $_POST['doi_tuong'] ?? 'SINH_VIEN',
    ];
}

function formFromNotification(array $notification): array
{
    return [
        'id' => (int) $notification['id'],
        'title' => $notification['tieu_de'],
        'description' => $notification['noi_dung'],
        'image_url' => $notification['image_url'],
        'doi_tuong' => $notification['doi_tuong'],
    ];
}

function validateNotification(array $form): array
{
    $errors = [];

    if ($form['title'] === '') {
        $errors['title'] = 'Vui lòng nhập tiêu đề.';
    } elseif (mb_strlen($form['title']) > 255) {
        $errors['title'] = 'Tiêu đề tối đa 255 ký tự.';
    }

    if ($form['description'] === '') {
        $errors['description'] = 'Vui lòng nhập nội dung.';
    }

    if (!filter_var($form['image_url'], FILTER_VALIDATE_URL)) {
        $errors['image_url'] = 'URL hình ảnh không hợp lệ.';
    }

    if (!in_array($form['doi_tuong'], ALLOWED_AUDIENCES, true)) {
        $errors['doi_tuong'] = 'Đối tượng không hợp lệ.';
    }

    return $errors;
}

function notificationData(array $form): array
{
    return [
        ':tieu_de' => $form['title'],
        ':noi_dung' => $form['description'],
        ':image_url' => $form['image_url'],
        ':doi_tuong' => $form['doi_tuong'],
    ];
}

function redirectWithMessage(string $message): void
{
    $_SESSION['flash'] = $message;
    header('Location: index.php');
    exit;
}

function verifyCsrfToken(): void
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postedToken = $_POST['csrf_token'] ?? '';

    if (!$sessionToken || !hash_equals($sessionToken, $postedToken)) {
        http_response_code(403);
        exit('Yêu cầu không hợp lệ.');
    }
}

// Khởi tạo dữ liệu dùng chung
$db = getDB();
$studentId = $_SESSION['ma_sv'] ?? DEFAULT_STUDENT_ID;

try {
    $sinh_vien = findStudent($db, $studentId);
} catch (PDOException $exception) {
    $sinh_vien = null;
}

$sinh_vien ??= [
    'ma_sv' => $studentId,
    'ho_ten' => 'Sinh Viên Demo',
    'ten_lop' => 'K65-CNTT',
    'ten_khoa' => 'Công nghệ thông tin',
];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$form = emptyNotificationForm();
$errors = [];

// Xử lý các thao tác CRUD gửi bằng POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $action = $_POST['action'] ?? 'create';
    $notificationId = filter_var(
        $_POST['id'] ?? 0,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    ) ?: 0;

    try {
        switch ($action) {
            case 'edit_form':
                $notification = findNotification($db, $notificationId);

                if ($notification) {
                    $form = formFromNotification($notification);
                }
                break;

            case 'delete':
                deleteNotification($db, $notificationId);
                redirectWithMessage('Đã xóa thông báo.');

            case 'toggle':
                toggleNotification($db, $notificationId);
                redirectWithMessage('Đã thay đổi trạng thái thông báo.');

            case 'create':
            case 'update':
                $form = formFromPost($notificationId);
                $errors = validateNotification($form);

                if (!$errors) {
                    $data = notificationData($form);

                    if ($action === 'update' && $notificationId > 0) {
                        updateNotification($db, $notificationId, $data);
                        redirectWithMessage('Cập nhật thông báo thành công.');
                    }

                    createNotification($db, $data);
                    redirectWithMessage('Thêm thông báo thành công.');
                }
                break;
        }
    } catch (PDOException $exception) {
        $errors['database'] = 'Không thể xử lý dữ liệu. Hãy kiểm tra cơ sở dữ liệu.';
    }
}

// Lấy dữ liệu để hiển thị
try {
    $notifications = getAllNotifications($db);
} catch (PDOException $exception) {
    $notifications = [];
    $errors['database'] = 'Không thể tải danh sách thông báo.';
}

$flashMessage = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$csrfToken = htmlspecialchars($_SESSION['csrf_token']);
$isEditing = $form['id'] > 0;

require_once 'includes/header.php';
?>

<section class="banner-title-row">
    <h2>QUẢN LÝ THÔNG BÁO</h2>
    <p class="subtitle-text">Thêm, xem, sửa, ẩn/hiện và xóa thông báo</p>
</section>

<div class="notifications-layout">
    <div class="notifications-list-col">
        <h3>DANH SÁCH THÔNG BÁO</h3>

        <?php if (empty($notifications)): ?>
            <div class="no-data-card">Chưa có thông báo.</div>
        <?php endif; ?>

        <?php foreach ($notifications as $notification): ?>
            <?php $isHidden = $notification['trang_thai'] === 'AN'; ?>

            <article class="notification-item-card <?= $isHidden ? 'is-hidden' : '' ?>">
                <div class="notification-img-wrapper">
                    <img
                        class="notification-img"
                        src="<?= htmlspecialchars($notification['image_url']) ?>"
                        alt="<?= htmlspecialchars($notification['tieu_de']) ?>"
                    >
                </div>

                <div class="notification-body">
                    <div>
                        <h4 class="notification-item-title">
                            <?= htmlspecialchars($notification['tieu_de']) ?>
                        </h4>
                        <p class="notification-item-desc">
                            <?= nl2br(htmlspecialchars($notification['noi_dung'])) ?>
                        </p>
                    </div>

                    <div class="notification-meta-row">
                        <span>#<?= (int) $notification['id'] ?></span>
                        <span><?= htmlspecialchars(getNotificationAudienceName($notification['doi_tuong'])) ?></span>
                        <span><?= htmlspecialchars($notification['trang_thai']) ?></span>
                        <span><?= date('d/m/Y H:i', strtotime($notification['ngay_tao'])) ?></span>
                    </div>

                    <div class="notification-actions">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <input type="hidden" name="action" value="edit_form">
                            <input type="hidden" name="id" value="<?= (int) $notification['id'] ?>">
                            <button class="btn-action" type="submit">Sửa</button>
                        </form>

                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= (int) $notification['id'] ?>">
                            <button class="btn-action" type="submit">
                                <?= $isHidden ? 'Mở khóa' : 'Khóa' ?>
                            </button>
                        </form>

                        <form method="post" onsubmit="return confirm('Xóa thông báo này?')">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $notification['id'] ?>">
                            <button class="btn-action btn-danger" type="submit">Xóa</button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <aside class="notifications-form-col">
        <h3><?= $isEditing ? 'SỬA THÔNG BÁO' : 'THÊM THÔNG BÁO' ?></h3>

        <?php if (isset($errors['database'])): ?>
            <p class="error-msg-notify"><?= htmlspecialchars($errors['database']) ?></p>
        <?php endif; ?>

        <form method="post" action="index.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
            <input type="hidden" name="id" value="<?= (int) $form['id'] ?>">

            <div class="form-group-notify">
                <label for="title">Tiêu đề</label>
                <input id="title" name="title" value="<?= htmlspecialchars($form['title']) ?>">
                <?php if (isset($errors['title'])): ?>
                    <span class="error-msg-notify"><?= htmlspecialchars($errors['title']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group-notify">
                <label for="description">Nội dung</label>
                <textarea id="description" name="description" rows="6"><?= htmlspecialchars($form['description']) ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <span class="error-msg-notify"><?= htmlspecialchars($errors['description']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group-notify">
                <label for="image_url">URL ảnh</label>
                <input id="image_url" name="image_url" value="<?= htmlspecialchars($form['image_url']) ?>">
                <?php if (isset($errors['image_url'])): ?>
                    <span class="error-msg-notify"><?= htmlspecialchars($errors['image_url']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group-notify">
                <label for="doi_tuong">Đối tượng</label>
                <select id="doi_tuong" name="doi_tuong">
                    <option value="SINH_VIEN" <?= $form['doi_tuong'] === 'SINH_VIEN' ? 'selected' : '' ?>>Sinh viên</option>
                    <option value="GIANG_VIEN" <?= $form['doi_tuong'] === 'GIANG_VIEN' ? 'selected' : '' ?>>Giảng viên</option>
                    <option value="TAT_CA" <?= $form['doi_tuong'] === 'TAT_CA' ? 'selected' : '' ?>>Tất cả</option>
                </select>
            </div>

            <button class="btn-submit-notify" type="submit">
                <?= $isEditing ? 'Lưu thay đổi' : 'Thêm thông báo' ?>
            </button>

            <?php if ($isEditing): ?>
                <a class="btn-cancel-edit" href="index.php">Hủy sửa</a>
            <?php endif; ?>
        </form>
    </aside>
</div>

<?php if ($flashMessage): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast(
                <?= json_encode($flashMessage, JSON_UNESCAPED_UNICODE) ?>,
                'success'
            );
        });
    </script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
