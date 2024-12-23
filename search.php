<?php
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "admin";
$password = "taphoahungvy@#.com";
$dbname = "test";
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra từ khóa tìm kiếm
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Truy vấn cơ sở dữ liệu nếu có từ khóa tìm kiếm
if ($query) {
    $sql = "SELECT id, name, description, price, image_url FROM Products WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_query = '%' . $query . '%';
    $stmt->bind_param('ss', $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null; // Nếu không có từ khóa tìm kiếm, không truy vấn cơ sở dữ liệu
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm</title>
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

            <!-- Toggler button for mobile view -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar collapse -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Thanh tìm kiếm -->
                <form class="d-flex mx-auto" method="GET" action="search.php">
                    <input class="form-control me-2" type="search" placeholder="Nhập từ khóa" aria-label="Search" name="query" style="width: 70%;">
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
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="nav-link" href="logout.php">Đăng xuất</a>
                        <?php else: ?>
                            <a class="nav-link" href="login.php">Đăng nhập</a>
                        <?php endif; ?>
                    </li>
                </ul>

                <!-- Các biểu tượng mạng xã hội -->
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
        <h1 class="text-center mb-4">Tìm kiếm</h1>

        <!-- Hiển thị kết quả tìm kiếm -->
        <?php if ($result && $result->num_rows > 0): ?>
            <!-- Hiển thị lưới sản phẩm -->
            <div class="row">
                <!-- Lặp qua các sản phẩm và hiển thị thông tin -->
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <!-- Hiển thị hình ảnh sản phẩm -->
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">

                            <!-- Hiển thị khung thông tin -->
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="price"><?php echo number_format($row['price'], 0, ',', '.') . " VND"; ?></p>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <!-- Nút "Thêm vào giỏ hàng" -->
                                <form method="POST" action="shop.php">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="hidden" name="add_to_cart" value="true">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bx bx-cart"></i> Thêm vào giỏ hàng
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- Không tìm thấy kết quả phù hợp -->
            <h3 class="text-center">Không tìm thấy sản phẩm.</h3>
        <?php endif; ?>

        <!-- Đóng kết nối cơ sở dữ liệu -->
        <?php
            $stmt->close();
            $conn->close();
        ?>
    </div>
</body>

</html>
