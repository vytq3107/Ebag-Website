<?php
session_start();
$servername = "localhost";
$username = "admin";
$password = "taphoahungvy@#.com";
$dbname = "test";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem người dùng đã đăng nhập hay chưa
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// kiểm tra và tạo giỏ hàng trong session nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
// Lấy user_id từ phiên làm việc
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Truy vấn để lấy cart_id và tổng giá từ bảng Carts
$sql = "SELECT id, total_price FROM Carts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_id, $total_price);
$stmt->fetch();
$stmt->close();


// Xử lý yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    // Lấy thông tin product_id và quantity từ yêu cầu POST
    $product_id = intval($_POST['product_id']);
    $new_quantity = intval($_POST['quantity']);

    // Lấy thông tin sản phẩm từ giỏ hàng
    $sql = "SELECT quantity, price FROM Cart_Items WHERE cart_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $stmt->bind_result($old_quantity, $price);
    $stmt->fetch();
    $stmt->close();

    // Nếu số lượng mới khác số lượng cũ
    if ($new_quantity !== $old_quantity) {
        // Cập nhật số lượng sản phẩm
        $sql = "UPDATE Cart_Items SET quantity = ? WHERE cart_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $new_quantity, $cart_id, $product_id);
        $stmt->execute();
        $stmt->close();

        // Tính toán tổng giá mới của sản phẩm
        $new_total_price_of_product = $new_quantity * $price;

        // Cập nhật tổng giá sản phẩm trong giỏ hàng
        $sql = "UPDATE Cart_Items SET total_price = ? WHERE cart_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $new_total_price_of_product, $cart_id, $product_id);
        $stmt->execute();
        $stmt->close();

        // Tính toán thay đổi trong tổng giá của giỏ hàng
        $difference = ($new_quantity - $old_quantity) * $price;

        // Cập nhật tổng giá của giỏ hàng
        $sql = "UPDATE Carts SET total_price = total_price + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $difference, $cart_id);
        $stmt->execute();
        $stmt->close();

        // Trả về phản hồi JSON với thông tin tổng giá sản phẩm và tổng giá giỏ hàng mới
        echo json_encode([
            'status' => 'success',
            'product_id' => $product_id,
            'new_quantity' => $new_quantity,
            'new_total_price' => number_format($new_total_price_of_product, 0, ',', '.') . ' VND',
            'cart_total' => number_format($total_price + $difference, 0, ',', '.') . ' VND'
        ]);
        exit();
    }
}

// Xử lý yêu cầu POST khi người dùng xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product']) && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    
    // Truy vấn để lấy thông tin tổng giá sản phẩm cần xóa
    $sql = "SELECT total_price FROM Cart_Items WHERE cart_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $stmt->bind_result($total_price_of_product);
    $stmt->fetch();
    $stmt->close();
    
    // Xóa sản phẩm khỏi giỏ hàng
    $sql = "DELETE FROM Cart_Items WHERE cart_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $stmt->close();
    
    // Cập nhật tổng giá trị giỏ hàng
    $sql = "UPDATE Carts SET total_price = total_price - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $total_price_of_product, $cart_id);
    $stmt->execute();
    $stmt->close();
    
    // Sau khi xóa, điều hướng lại trang cart.php
    header("Location: cart.php");
    exit();
}


// Truy vấn các sản phẩm trong giỏ hàng từ bảng Cart_Items và Products
$sql = "SELECT ci.product_id, p.name, p.image_url, ci.quantity, ci.price, ci.total_price
        FROM Cart_Items ci
        JOIN Products p ON ci.product_id = p.id
        WHERE ci.cart_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$stmt->bind_result($product_id, $name, $image_url, $quantity, $price, $item_total_price);


$homeURL = "index.php";
$shopURL = "shop.php";
$cartURL = "cart.php";
$loginURL = "login.php";
$logoutURL = "logout.php";

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script>
        // Hàm để gửi yêu cầu POST khi người dùng thay đổi số lượng
        function updateQuantity(product_id, quantity) {
            // Gửi yêu cầu POST với thông tin product_id và quantity
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${product_id}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Cập nhật tổng giá trị giỏ hàng và tổng giá sản phẩm trong giao diện
                    document.getElementById(`product-${data.product_id}-total`).textContent = data.new_total_price;
                    document.getElementById('cart-total').textContent = `Tổng giá trị giỏ hàng: ${data.cart_total}`;
                } else {
                    alert('Có lỗi xảy ra khi cập nhật số lượng.');
                }
            });
        }
    </script>
</head>

<body>
    <!-- Header -->
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
                            <a class="nav-link" aria-current="page" href="<?php echo $homeURL; ?>">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $shopURL; ?>">Cửa hàng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo $cartURL; ?>">Giỏ hàng</a>
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

    <!-- Nội dung chính -->
    <div class="container my-4">
        <h1 class="text-center mb-4">Giỏ hàng</h1>

        <!-- Kiểm tra xem giỏ hàng có trống hoặc tổng giá trị giỏ hàng bằng 0 -->
        <?php if (!$cart_id || $total_price == 0): ?>
            <p>Giỏ hàng của bạn đang trống.</p>
            <a href="shop.php" class="btn btn-primary">Tiếp tục mua sắm</a>
        <?php else: ?>

            <!-- Nếu giỏ hàng không trống, hiển thị các sản phẩm trong giỏ hàng -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($stmt->fetch()): ?>
                        <tr>
                            <td><img src="<?php echo $image_url; ?>" alt="<?php echo $name; ?>" style="width: 80px; height: 80px; object-fit: cover;"></td>
                            <td><?php echo $name; ?></td>
                            <td><?php echo number_format($price, 0, ',', '.') . " VND"; ?></td>
                            <td>
                                <input type="number" id="product-<?php echo $product_id; ?>-quantity" value="<?php echo $quantity; ?>" min="1" class="form-control" style="width: 60px; display: inline-block;" onchange="updateQuantity(<?php echo $product_id; ?>, this.value)">
                            </td>
                            <td id="product-<?php echo $product_id; ?>-total"><?php echo number_format($item_total_price, 0, ',', '.') . " VND"; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_product" value="true">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Hiển thị tổng giá giỏ hàng -->
            <p id="cart-total"><strong>Tổng giá trị giỏ hàng:</strong> <?php echo number_format($total_price, 0, ',', '.') . " VND"; ?></p>

            <!-- Nút thanh toán -->
            <form method="POST" action="checkout.php">
                <button type="submit" class="btn btn-primary">Thanh toán</button>
            </form>


        <?php endif; ?>
    </div>
              
    <!-- Đóng kết nối cơ sở dữ liệu -->
    <?php
        $stmt->close();
        $conn->close();
    ?>
</body>

</html>
