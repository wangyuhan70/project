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

$result = $conn->query("SELECT DISH_ID, DISH_NAME FROM DISH ORDER BY DISH_NAME ASC");
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <title>全部食譜</title>
</head>
<body>
  <h1>📖 食譜總覽</h1>

  <?php if ($result->num_rows === 0): ?>
    <p>目前尚無任何食譜資料。</p>
  <?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <form action="dish_detail.php" method="get" style="margin-bottom: 10px;">
        <input type="hidden" name="dish_id" value="<?php echo $row['DISH_ID']; ?>">
        <button type="submit" style="padding: 10px 20px; font-size: 16px;">
          🍲 <?php echo htmlspecialchars($row['DISH_NAME']); ?>
        </button>
      </form>
    <?php endwhile; ?>
  <?php endif; ?>

  <br><a href="index.html">← 回首頁</a>
</body>
</html>

<?php $conn->close(); ?>
