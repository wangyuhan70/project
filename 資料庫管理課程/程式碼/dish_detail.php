<?php
$host = "127.0.0.1";
$user = "root";
$db_password = "";
$database = "GOODIDEA";

$conn = new mysqli($host, $user, $db_password, $database);
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$dish_id = $_GET['dish_id'] ?? '';
if ($dish_id === '') {
    die("未提供食譜 ID");
}

$stmt = $conn->prepare("SELECT DISH_NAME, DISH_DESCRIPTION FROM DISH WHERE DISH_ID = ?");
$stmt->bind_param("i", $dish_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("找不到該食譜");
}

$dish = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($dish['DISH_NAME']); ?></title>
</head>
<body>
  <h1><?php echo htmlspecialchars($dish['DISH_NAME']); ?></h1>
  <p><?php echo nl2br(htmlspecialchars($dish['DISH_DESCRIPTION'])); ?></p>

  <br><a href="dish_list.php">← 回食譜總覽</a>
</body>
</html>
