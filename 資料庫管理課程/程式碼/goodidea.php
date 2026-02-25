<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<script>alert('goodidea.php 被執行');</script>";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $phonenumber = $_POST['phonenumber'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $phonenumber === '' || $password === '') {
        die("請確認所有欄位皆已填寫");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $host = "127.0.0.1";
    $user = "root";
    $db_password = "";
    $database = "GOODIDEA";

    $conn = new mysqli($host, $user, $db_password, $database);

    if ($conn->connect_error) {
        die("資料庫連線失敗：" . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO MAINUSER (USER_NAME, USER_PHONE_NUM, USER_PASSWORD) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("預處理失敗：" . $conn->error);
    }

    $stmt->bind_param("sss", $username, $phonenumber, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('資料寫入成功'); window.location.href='index.html';</script>";
    } else {
        echo "寫入失敗：" . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "非 POST 方法";
}
?>
