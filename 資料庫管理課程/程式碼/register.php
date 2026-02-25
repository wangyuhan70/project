
資料夾摘要
SQL scripts and PHP files support a food recipe and purchasing system, featuring user management and ingredient storage tracking.

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// 接收資料
$username = $_POST['username'] ?? '';
$phonenumber = $_POST['phonenumber'] ?? '';
$password = $_POST['password'] ?? '';

// 檢查是否有填寫
if ($username === '' || $phonenumber === '' || $password === '') {
    die("所有欄位都必須填寫");
}

// 密碼加密
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 資料庫設定
$host = "127.0.0.1";
$db_user = "root";
$db_password = "";
$database = "GOODIDEA";

$conn = new mysqli($host, $db_user, $db_password, $database);
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 檢查手機是否已註冊
$check_stmt = $conn->prepare("SELECT * FROM MAINUSER WHERE USER_PHONE_NUM = ?");
$check_stmt->bind_param("s", $phonenumber);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "<script>alert('⚠️ 此手機號已註冊，請直接登入'); window.location.href = 'index.html';</script>";
    exit();
}
$check_stmt->close();

// 取得下一個 ID（自動遞增或自行處理）
$get_max_id = $conn->query("SELECT MAX(USER_ID) AS max_id FROM MAINUSER");
$row = $get_max_id->fetch_assoc();
$new_user_id = ($row['max_id'] ?? 10000) + 1;  
$get_max_id->close();

// 寫入資料
$stmt = $conn->prepare("INSERT INTO MAINUSER (USER_ID, USER_NAME, USER_PHONE_NUM, USER_PASSWORD) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $new_user_id, $username, $phonenumber, $hashed_password);

if ($stmt->execute()) {
    echo "<script>alert('註冊成功，請登入！'); window.location.href = 'index.html';</script>";
} else {
    echo "寫入失敗：" . $stmt->error;
}

$stmt->close();
$conn->close();
?>
