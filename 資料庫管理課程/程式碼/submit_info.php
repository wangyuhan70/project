
資料夾摘要
SQL scripts and PHP files support a food recipe and purchasing system, featuring user management and ingredient storage tracking.

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die(" 請先登入");
}

// 接收表單資料
$user_id = $_SESSION['user_id'];
$store_amount = $_POST['store_amount'] ?? '';
$ingredient_id = $_POST['ingredient_id'] ?? '';
$store_time = $_POST['store_time'] ?? date('Y-m-d'); // 若沒填預設今天
$store_exp = $_POST['store_exp'] ?? '';

// 基本檢查
if ($store_amount === '' || $ingredient_id === ''  || $store_exp === '') {
    die("所有欄位皆為必填");
}

$host = "127.0.0.1";
$user = "root";
$db_password = "";
$database = "GOODIDEA";

$conn = new mysqli($host, $user, $db_password, $database);
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 寫入 STORE 表
$stmt = $conn->prepare("
  INSERT INTO STORE (USER_ID, STORE_AMOUNT, INGREDIENT_ID, STORE_TIME,  STORE_EXP)
  VALUES (?, ?, ?, ?,?)
");

if (!$stmt) {
    die("預處理失敗：" . $conn->error);
}

$stmt->bind_param("isiss", $user_id, $store_amount, $ingredient_id, $store_time,  $store_exp);

if ($stmt->execute()) {
    echo "<script>alert('資料已儲存'); window.location.href='member.php';</script>";
} else {
    echo "寫入失敗：" . $stmt->error;
}

$stmt->close();
$conn->close();
?>
