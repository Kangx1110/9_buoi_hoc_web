<?php

require_once "functions.php";

$notifications = [
    [
        "id" => 1,
        "title" => "Công khai luận án",
        "description" => "Công khai thông tin luận án Tiến sĩ của nghiên cứu sinh Trần Thị Thịnh trước khi bảo vệ luận án cấp Trường Đại học Thủ đô Hà Nội",
        "image_url" => "https://hnmu.edu.vn/sites/default/files/2026-08/anh-cong-khai.png"
    ],
    [
        "id" => 2,
        "title" => "Thông điệp",
        "description" => "Thông điệp năm học 2026 - 2027 của Hiệu trưởng",
        "image_url" => "https://hnmu.edu.vn/sites/default/files/2026-08/logo-hnmu-1.ai_.png"
    ],
    [
        "id" => 3,
        "title" => "Thông báo học phí",
        "description" => "Nhà trường thông báo thời gian nộp học phí cho học kỳ mới. Sinh viên cần hoàn thành đúng thời hạn.",
        "image_url" => "https://hnmu.edu.vn/sites/default/files/2026-06/quyet-dinh_4.jpg"
    ]
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $image_url = trim($_POST["image_url"]);


    /*
     * Tự động tạo ID
     * Ví dụ đang có 3 thông báo thì
     * thông báo mới sẽ có ID = 4
     */

    $id = count($notifications) + 1;


    $notifications[] = [
        "id" => $id,
        "title" => $title,
        "description" => $description,
        "image_url" => $image_url
    ];
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Thông báo - Quản lý khóa học</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">
    <div class="header">
        <div class="small-title">
            NOTIFICATIONS
        </div>
        <h1>
            Thông báo
        </h1>
        <p>
            Cập nhật những thông tin mới nhất về khóa học
            và đăng ký học phần.
        </p>
    </div>

    <div class="form-container">
        <h2>
            Thêm thông báo
        </h2>
        <form method="POST">
            <div class="form-group">
                <label>
                    Tiêu đề
                </label>
                <input
                    type="text"
                    name="title"
                    placeholder="Nhập tiêu đề thông báo"
                    required
                >
            </div>

            <div class="form-group">
                <label>
                    Nội dung
                </label>
                <textarea
                    name="description"
                    placeholder="Nhập nội dung thông báo"
                    required
                ></textarea>
            </div>

            <div class="form-group">
                <label>
                    Image URL
                </label>
                <input
                    type="text"
                    name="image_url"
                    placeholder="Nhập đường dẫn hình ảnh"
                    required
                >
            </div>

            <button type="submit">
                Thêm thông báo
            </button>
        </form>
    </div>

    <section class="notification-section">
        <div class="section-title">
            <span>
                MY NOTIFICATIONS
            </span>
            <h2>
                Thông báo mới
            </h2>
        </div>

        <div class="notification-grid">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card">
                    <div class="notification-image">
                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $notification["image_url"]
                                );
                            ?>"
                            alt="Notification Image"
                        >
                    </div>

                    <div class="notification-content">
                        <h3>
                            <?php

                            echo htmlspecialchars(
                                $notification["title"]
                            );

                            ?>
                        </h3>
                        <p>
                            <?php
                            echo htmlspecialchars(
                                $notification["description"]
                            );
                            ?>
                        </p>
                        <div class="notification-footer">
                            <span class="tag">
                                <?php
                                echo getNotificationStatus(
                                    $notification["title"],
                                    $notification["description"]
                                );
                                ?>
                            </span>
                            <span class="notification-id">
                                #
                                <?php
                                echo htmlspecialchars(
                                    $notification["id"]
                                );
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
</body>
</html>