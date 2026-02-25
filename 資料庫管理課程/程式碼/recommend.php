
資料夾摘要
SQL scripts and PHP files support a food recipe and purchasing system, featuring user management and ingredient storage tracking.

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
    die("\u8cc7\u6599\u5eab\u9023\u7dda\u5931\u6557：" . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$user_id = $_SESSION['user_id'];

$sql = "
SELECT 
    D.DISH_ID,
    D.DISH_NAME,
    D.DISH_DESCRIPTION,
    COUNT(*) AS matched_valid_ingredients
FROM 
    DISH D
JOIN 
    DISH_INGRE DI ON D.DISH_ID = DI.DISH_ID
JOIN 
    INGREDIENT I ON DI.INGREDIENT_ID = I.INGREDIENT_ID
JOIN 
    STORE S ON I.INGREDIENT_ID = S.INGREDIENT_ID
WHERE 
    S.USER_ID = ?
    AND I.INGREDIENT_CTG_ID NOT IN (4, 5)
GROUP BY 
    D.DISH_ID, D.DISH_NAME, D.DISH_DESCRIPTION
HAVING 
    COUNT(*) >= 2
ORDER BY 
    matched_valid_ingredients DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <title>推薦食譜</title>
</head>
<body>
  <h1>您好， <?php echo htmlspecialchars($_SESSION['user_name']); ?>！</h1>
  <h2>以下是根據您目前持有的食材推薦的食譜：</h2>

  <?php if ($result->num_rows === 0): ?>
    <p>目前沒有可推薦的食譜。建議您新增更多食材！</p>
  <?php else: ?>
    <ul>
      <?php while ($row = $result->fetch_assoc()): ?>
        <li>
          <strong><?php echo htmlspecialchars($row['DISH_NAME']); ?></strong><br>
          <?php echo nl2br(htmlspecialchars($row['DISH_DESCRIPTION'])); ?><br>
          <em>符合食材數量（不含第4與5類）：<?php echo $row['matched_valid_ingredients']; ?></em>
        </li>
        <hr>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>

  <br><a href="member.php">← 回會員頁</a>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
