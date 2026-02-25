
資料夾摘要
SQL scripts and PHP files support a food recipe and purchasing system, featuring user management and ingredient storage tracking.

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "127.0.0.1";
$user = "root";
$db_password = "";
$database = "GOODIDEA";

$conn = new mysqli($host, $user, $db_password, $database);
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

$phonenumber = $_POST['phonenumber'] ?? '';
$password = $_POST['password'] ?? '';
$username = $_POST['username'] ?? '';


$sql = "SELECT * FROM MAINUSER WHERE USER_PHONE_NUM = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $phonenumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['USER_PASSWORD'])) {
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['user_name'] = $user['USER_NAME'];
        $_SESSION['phonenumber'] = $user['USER_PHONE_NUM'];
        header("Location: member.php");  
        exit();
    } else {
        echo "<script>alert('密碼錯誤'); history.back();</script>";
    }
} else {
    echo "<script>alert('使用者不存在'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>
