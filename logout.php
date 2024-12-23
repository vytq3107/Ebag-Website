<?php
session_start();

// Xóa tất cả các biến phiên làm việc liên quan đến người dùng
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);

// Hủy phiên làm việc
session_destroy();

//chuyển hướng đến index.php
header("Location: index.php");
exit();
?>
