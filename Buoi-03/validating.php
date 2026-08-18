<?php

$fullname = "";
$email = "";
$subject = "";
$content = "";
$image_url = "";

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Nhận dữ liệu
    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $content = trim($_POST["content"] ?? "");
    $image_url = trim($_POST["image_url"] ?? "");


    // 1. Kiểm tra họ tên không được rỗng
    if ($fullname === "") {

        $error = "Họ tên không được để trống.";

    // Kiểm tra họ tên chỉ chứa chữ cái và khoảng trắng
    } elseif (!preg_match("/^[a-zA-ZÀ-ỹ\s]+$/u", $fullname)) {

        $error = "Họ tên chỉ được chứa chữ cái và khoảng trắng.";

    // 2. Kiểm tra nội dung không được rỗng
    } elseif ($content === "") {

        $error = "Nội dung không được để trống.";

    // 3. Kiểm tra email
    } elseif (!preg_match(
        "/^[a-zA-Z0-9.]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
        $email
    )) {

        $error = "Email không đúng định dạng.";

    // 3 kiểm tra bắt buộc cho ảnh
    // Kiểm tra 1: Không được bỏ trống
    } elseif ($image_url === "") {

        $error = "Vui lòng nhập link ảnh đại diện.";

    // Kiểm tra 2: Phải là URL
    } elseif (!filter_var($image_url, FILTER_VALIDATE_URL)) {

        $error = "Link ảnh không đúng định dạng URL.";

    // Kiểm tra 3: Phải là link ảnh
    } elseif (!preg_match(
        '/\.(jpg|jpeg|png|gif)$/i',
        parse_url($image_url, PHP_URL_PATH)
    )) {

        $error = "Link phải là ảnh JPG, JPEG, PNG hoặc GIF.";

    } else {

        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Form thông tin</title>

    <style>
        body {
            font-family: Arial;
            width: 700px;
            margin: 30px auto;
        }

        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        textarea {
            height: 100px;
        }

        button {
            padding: 10px 20px;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
        }

        .result {
            border: 1px solid #ccc;
            padding: 20px;
            margin-top: 30px;
        }

        .result img {
            max-width: 250px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<h2>FORM GỬI THÔNG TIN</h2>

<?php if ($error !== "") { ?>

    <p class="error">
        <?php echo $error; ?>
    </p>

<?php } ?>


<form method="POST">

    <label>Họ tên:</label>
    <input
        type="text"
        name="fullname"
        value="<?php echo htmlspecialchars($fullname); ?>"
    >

    <br>

    <label>Email:</label>
    <input
        type="text"
        name="email"
        value="<?php echo htmlspecialchars($email); ?>"
    >

    <br>

    <label>Chủ đề:</label>
    <input
        type="text"
        name="subject"
        value="<?php echo htmlspecialchars($subject); ?>"
    >

    <br>

    <label>Nội dung:</label>
    <textarea name="content"><?php
        echo htmlspecialchars($content);
    ?></textarea>

    <br>

    <label>Link ảnh đại diện:</label>
    <input
        type="text"
        name="image_url"
        value="<?php echo htmlspecialchars($image_url); ?>"
        placeholder="https://example.com/image.jpg"
    >

    <br>

    <button type="submit">Gửi thông tin</button>

</form>


<?php if ($success) { ?>

    <div class="result">

        <h2 class="success">
            Gửi thông tin thành công!
        </h2>

        <h3>Kết quả:</h3>

        <p>
            <strong>Họ tên:</strong>
            <?php echo htmlspecialchars($fullname); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($email); ?>
        </p>

        <p>
            <strong>Chủ đề:</strong>
            <?php echo htmlspecialchars($subject); ?>
        </p>

        <p>
            <strong>Nội dung:</strong>
            <?php echo nl2br(htmlspecialchars($content)); ?>
        </p>

        <p>
            <strong>Ảnh đại diện:</strong>
        </p>

        <img
            src="<?php echo htmlspecialchars($image_url); ?>"
            alt="Ảnh đại diện"
        >

    </div>

<?php } ?>

</body>
</html>