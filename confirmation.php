<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy trạng thái thanh toán từ URL
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Hiển thị thông báo phù hợp dựa trên trạng thái thanh toán
$message = '';
if ($status === 'success') {
    $message = 'Thanh toán của bạn đã thành công. Cảm ơn bạn đã mua sắm!';
} elseif ($status === 'failure') {
    $message = 'Thanh toán của bạn đã thất bại. Vui lòng thử lại.';
} else {
    $message = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xác nhận thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel='stylesheet'>
    <link href="style.css" rel='stylesheet'>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bx bxs-shopping-bag"></i>
                <span class="ms-4">EBag</span>
            </a>

            <!--Mobile view-->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar collapse -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Thanh tìm kiếm -->
                <form class="d-flex mx-auto" role="search" method="GET" action="search.php">
                    <input class="form-control me-2" type="search" placeholder="Nhập từ khóa" aria-label="Search" name="query">
                    <button class="btn btn-outline-success" type="submit">Tìm kiếm</button>
                </form>

                <!-- Các nút điều hướng -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Cửa hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Giỏ hàng</a>
                    </li>
                    <li class="nav-item">
                        <?php if (isLoggedIn()): ?>
                            <a class="nav-link" href="logout.php">Đăng xuất</a>
                        <?php else: ?>
                            <a class="nav-link" href="login.php">Đăng nhập</a>
                        <?php endif; ?>
                    </li>
                </ul>

                <!-- Mạng xã hội -->
                <ul class="navbar-social-icons">
                        <li class="nav-item">
                            <a class="nav-link" href="https://www.facebook.com/profile.php?id=61555541295987" target="_blank" aria-label="Facebook">
                                <i class="bx bxl-facebook"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://www.instagram.com/e___bag" target="_blank" aria-label="Instagram">
                                <i class="bx bxl-instagram"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://www.tiktok.com/@eba__gg?_t=8n189lgqH1l&_r=1" target="_blank" aria-label="TikTok">
                                <i class="bx bxl-tiktok"></i>
                            </a>
                        </li>
                    </ul>
            </div>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <div class="container my-4">
        <h1 class="text-center mb-4">Xác nhận thanh toán</h1>

        <!-- Hiển thị thông báo dựa trên trạng thái thanh toán -->
        <div class="alert alert-<?php echo $status === 'success' ? 'success' : 'danger'; ?>">
            <?php echo $message; ?>
        </div>

        <!-- Nút quay lại trang chủ -->
        <a href="index.php" class="btn btn-primary">Quay lại trang chủ</a>
    </div>

</body>

</html>
