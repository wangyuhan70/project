<?php
session_start();          // 開始 session
session_unset();          // 清除所有 session 變數
session_destroy();        // 銷毀 session

// 跳回首頁或登入頁
header("Location: index.html"); 
exit();
?>
