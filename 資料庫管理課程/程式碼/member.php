
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
    die("資料庫連線失敗: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
$user_id = $_SESSION['user_id'];

// 處理刪除
if (isset($_GET['delete'])) {
    $ingredient_id = $_GET['delete'];
    $store_time = $_GET['time'];
    $stmt = $conn->prepare("DELETE FROM STORE WHERE USER_ID = ? AND INGREDIENT_ID = ? AND STORE_TIME = ?");
    $stmt->bind_param("iis", $user_id, $ingredient_id, $store_time);
    $stmt->execute();
    $stmt->close();
    header("Location: member.php");
    exit();
}

// 查詢使用者食材資料
$sql = "
SELECT S.INGREDIENT_ID, I.INGREDIENT_NAME, S.STORE_AMOUNT, S.STORE_TIME, S.STORE_EXP
FROM STORE S
JOIN INGREDIENT I ON S.INGREDIENT_ID = I.INGREDIENT_ID
WHERE S.USER_ID = ?
ORDER BY S.STORE_EXP ASC
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
    <title>會員儲存食材頁</title>
</head>
<body>
<h1>歡迎，使用者 <?php echo htmlspecialchars($_SESSION['user_name']); ?>！</h1>

<h2>填寫儲存的食材資訊</h2>
<form action="submit_info.php" method="post">
    <label>儲存數量（例如 3包）：</label><br>
    <input type="text" name="store_amount" required><br><br>

    <label>選擇食材：</label><br>
    <select name="ingredient_id" required>
      <option value="">-- 請選擇食材 --</option>
      <?php
      $ing_result = $conn->query("SELECT INGREDIENT_ID, INGREDIENT_NAME FROM INGREDIENT");
      while ($ing = $ing_result->fetch_assoc()): ?>
        <option value="<?php echo $ing['INGREDIENT_ID']; ?>">
          <?php echo htmlspecialchars($ing['INGREDIENT_NAME']); ?>
        </option>
      <?php endwhile; ?>
    </select><br><br>

    <label>儲存日期（STORE_TIME）：</label><br>
    <input type="date" name="store_time" value="<?php echo date('Y-m-d'); ?>" required><br><br>

    <label>到期日（STORE_EXP）：</label><br>
    <input type="date" name="store_exp" required><br><br>

    <button type="submit">儲存食材資料</button>
</form>

<h2>我的食材清單</h2>
<?php if ($result->num_rows === 0): ?>
    <p>您目前尚未儲存任何食材。</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>食材名稱</th>
                <th>數量</th>
                <th>儲存日期</th>
                <th>到期日</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['INGREDIENT_NAME']); ?></td>
                <td><?php echo htmlspecialchars($row['STORE_AMOUNT']); ?></td>
                <td><?php echo htmlspecialchars($row['STORE_TIME']); ?></td>
                <td><?php echo htmlspecialchars($row['STORE_EXP']); ?></td>
                <td>
                    <a href="edit_store.php?ingredient_id=<?php echo $row['INGREDIENT_ID']; ?>&time=<?php echo $row['STORE_TIME']; ?>">編輯</a> |
                    <a href="?delete=<?php echo $row['INGREDIENT_ID']; ?>&time=<?php echo $row['STORE_TIME']; ?>" onclick="return confirm('確定要刪除這筆資料嗎？');">刪除</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="recommend.php">👉 查看推薦食譜</a></p>
<p><a href="logout.php">登出</a></p>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
