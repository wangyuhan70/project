<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$host = "127.0.0.1";
$user = "root";
$db_password = "";
$database = "GOODIDEA";

$conn = new mysqli($host, $user, $db_password, $database);
if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
$user_id = $_SESSION['user_id'];
$ingredient_id = $_GET['ingredient_id'] ?? null;
$store_time = $_GET['time'] ?? null;

if (!$ingredient_id || !$store_time) {
    die("缺少參數");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_amount = $_POST['store_amount'] ?? '';
    $store_exp = $_POST['store_exp'] ?? '';

    if ($store_amount === '' || $store_exp === '') {
        die(" 所有欄位皆為必填");
    }

    $stmt = $conn->prepare("UPDATE STORE SET STORE_AMOUNT = ?, STORE_EXP = ? WHERE USER_ID = ? AND INGREDIENT_ID = ? AND STORE_TIME = ?");
    $stmt->bind_param("ssiis", $store_amount, $store_exp, $user_id, $ingredient_id, $store_time);

    if ($stmt->execute()) {
        echo "<script>alert('更新成功'); window.location.href='member.php';</script>";
    } else {
        echo "更新失敗：" . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit();
}

// 取得現有資料供預填
$stmt = $conn->prepare("SELECT STORE_AMOUNT, STORE_EXP FROM STORE WHERE USER_ID = ? AND INGREDIENT_ID = ? AND STORE_TIME = ?");
$stmt->bind_param("iis", $user_id, $ingredient_id, $store_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("找不到這筆資料");
}

$data = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>編輯食材</title>
</head>
<body>
<h1>編輯食材資料</h1>
<form action="" method="post">
    <label>儲存數量：</label><br>
    <input type="text" name="store_amount" value="<?php echo htmlspecialchars($data['STORE_AMOUNT']); ?>" required><br><br>

    <label>到期日：</label><br>
    <input type="date" name="store_exp" value="<?php echo htmlspecialchars($data['STORE_EXP']); ?>" required><br><br>

    <button type="submit">更新</button>
    <a href="member.php">取消</a>
</form>
</body>
</html>
