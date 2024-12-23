<?php
session_start();
$servername = "localhost"; //edit
$username = "admin"; //edit
$password = "admin"; //edit
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
$user_id = $_SESSION['user_id'];



// Lấy thông tin giỏ hàng từ cơ sở dữ liệu
$sql = "SELECT ci.product_id, p.name, p.image_url, ci.quantity, ci.price, ci.total_price
        FROM Cart_Items ci
        JOIN Products p ON ci.product_id = p.id
        WHERE ci.cart_id = (SELECT id FROM Carts WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra tổng giá trị giỏ hàng
$sql = "SELECT id, total_price FROM Carts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_id, $total_price);
$stmt->fetch();
$stmt->close();




// Xử lý yêu cầu POST từ biểu mẫu thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $shipping_address = $_POST['shipping_address'] ?? '';
    $order_note = $_POST['order_note'] ?? '';
    $recipient_name = $_POST['recipient_name'] ?? '';
    $recipient_phone = $_POST['recipient_phone'] ?? '';
    
    // Kiểm tra phương thức thanh toán
    if ($payment_method === 'cod') {
        // Xử lý thanh toán khi nhận hàng (COD)
        
        // Thêm đơn hàng vào bảng Orders
        $sql = "INSERT INTO Orders (user_id, cart_id, order_date, payment_method, total_price, order_note, shipping_address, recipient_name, recipient_phone)
            VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisdsdss", $user_id, $cart_id, $payment_method, $total_price, $order_note, $shipping_address, $recipient_name, $recipient_phone);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Lấy ID của đơn hàng vừa chèn
        $stmt->close();

        // Thêm các mặt hàng vào bảng Order_Items
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];
            $price = $row['total_price'];

            $sql = "INSERT INTO Order_Items (order_id, product_id, quantity, total_price, created_at)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
            $stmt->close();
        }

        // Sau khi hoàn tất, xóa giỏ hàng
        $sql = "DELETE FROM Cart_Items WHERE cart_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        $stmt->close();
        // Đặt lại tổng giá của giỏ hàng về 0
        $sql = "UPDATE Carts SET total_price = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        $stmt->close();

         // Sau khi hoàn tất quá trình thanh toán và xử lý đơn hàng
        echo "<script>
        alert('Đặt hàng thành công!');
        window.location.href = 'shop.php';
        </script>";
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel='stylesheet'>
    <link href="style.css" rel='stylesheet'>
</head>

<body>
    <!-- Header -->
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

            <!-- Navbar collapse -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Thanh tìm kiếm -->
                <form class="d-flex mx-auto" role="search" method="GET" action="search.php">
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

    <!-- Nội dung -->
    <div class="container my-4">
        <h1 class="text-center mb-4">Thanh toán</h1>

        <!-- Hiển thị thông tin giỏ hàng -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Lặp qua các sản phẩm trong giỏ hàng và hiển thị thông tin
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>" style="width: 80px; height: 80px; object-fit: cover;"></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo number_format($row['price'], 0, ',', '.') . " VND"; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo number_format($row['total_price'], 0, ',', '.') . " VND"; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Hiển thị tổng giá trị giỏ hàng -->
        <p><strong>Tổng giá trị giỏ hàng:</strong> <?php echo number_format($total_price, 0, ',', '.') . " VND"; ?></p>
        
        <!-- Form thanh toán -->
        <form method="POST" action="checkout.php">
            <!-- Phương thức thanh toán -->
            <div class="mb-3">
                <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                </select>
            </div>

            <!-- Thông tin người nhận -->
            <div class="mb-3">
                <label for="recipient_name" class="form-label">Tên người nhận</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
            </div>
            <div class="mb-3">
                <label for="recipient_phone" class="form-label">Số điện thoại người nhận</label>
                <input type="text" class="form-control" id="recipient_phone" name="recipient_phone" required>
            </div>

            <!-- Địa chỉ giao hàng -->
            <div class="mb-3">
                <label for="shipping_address" class="form-label">Địa chỉ giao hàng</label>
                <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required></textarea>
            </div>

            <!-- Ghi chú đơn hàng -->
            <div class="mb-3">
                <label for="order_note" class="form-label">Ghi chú đơn hàng</label>
                <textarea class="form-control" id="order_note" name="order_note" rows="3"></textarea>
            </div>

            <!-- Nút thanh toán -->
            <button type="submit" class="btn btn-primary">Xác nhận</button>
            </form>

    </div>

</body>

</html>
