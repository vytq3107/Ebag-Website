<?php
session_start();

// Kiểm tra và tạo giỏ hàng trong session nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Hàm để kiểm tra xem người dùng đã đăng nhập hay chưa
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

$homeURL = "index.php";
$shopURL = "shop.php";
$cartURL = "cart.php";
$loginURL = "login.php";
$logoutURL = "logout.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="style.css" rel='stylesheet'>
    
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo $homeURL; ?>">
                    <div class="logo-container">
                        <img src="/images/logo.png" alt="EBag Logo" class="logo-image">
                    </div>
                    <span class="ms-4">EBag</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <form class="d-flex mx-auto" role="search" method="GET" action="search.php">
                        <input class="form-control me-2" type="search" placeholder="Nhập từ khóa" aria-label="Search"
                            name="query" style="width: 70%;">
                        <button class="btn btn-outline-success" type="submit">Tìm kiếm</button>
                    </form>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?php echo $homeURL; ?>">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $shopURL; ?>">Cửa hàng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $cartURL; ?>">Giỏ hàng</a>
                        </li>
                        <li class="nav-item">
                            <?php if (isLoggedIn()): ?>
                                <a class="nav-link" href="<?php echo $logoutURL; ?>">Đăng xuất</a>
                            <?php else: ?>
                                <a class="nav-link" href="<?php echo $loginURL; ?>">Đăng nhập</a>
                            <?php endif; ?>
                        </li>
                    </ul>

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
    </header>

    <section id="hot-products" class="section active">
        <div class="container section-content">
            <div class="row">
                <!-- Phần bên trái là chữ -->
                <div class="col-md-6 d-flex align-items-center">
                    <div>
                        <h2 class="mb-4">Sản phẩm nổi bật</h2>
                        <p class="lead mb-4">Trong bối cảnh toàn cầu đang chú trọng hơn bao giờ hết đến vấn đề bảo vệ môi trường, EBag tự hào giới thiệu bộ sưu tập "Eco" nhân Ngày Bảo Vệ Môi Trường. Bộ sưu tập này không chỉ là một dòng sản phẩm mới mà còn là sự kết hợp giữa túi tote và những items thiên nhiên, độc đáo nhằm nhấn mạnh tầm quan trọng của việc bảo vệ môi trường. </p>
                    </div>
                </div>
                <!-- Phần bên phải là hình ảnh -->
                <div class="col-md-6">
                    <div class="stacked-images">
                        <img src="/images/feed1.png" class="img-fluid stacked-img" alt="Hot Product 1">
                        <img src="/images/feed2.png" class="img-fluid stacked-img" alt="Hot Product 2">
                        <img src="/images/feed3.png" class="img-fluid stacked-img" alt="Hot Product 3">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <h2 class="text-center mb-4">Về chúng tôi</h2>
            <p class="lead">Hãy đến và trải nghiệm sự khác biệt mà EBag mang lại, không chỉ trong phong cách mà còn trong cách chúng ta bảo vệ môi trường. Cùng nhau, chúng ta có thể tạo nên một thế giới tốt đẹp hơn cho thế hệ mai sau</p>
            <p class="text-center">&copy; 2024 EBag</p>
        </div>
    </footer>
    <script>
        $(document).ready(function () {
            // Activate ScrollSpy
            $('body').scrollspy({
                target: ".navbar",
                offset: 50
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
