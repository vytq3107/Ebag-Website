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

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Lấy user_id
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Kiểm tra và tạo giỏ hàng cho người dùng nếu chưa có
$cart_id = null;
if ($user_id) {
    // Kiểm tra xem người dùng đã có giỏ hàng chưa
    $sql = "SELECT id FROM Carts WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cart_id);
    $stmt->fetch();
    $stmt->close();

    // Nếu người dùng chưa có giỏ hàng, tạo một giỏ hàng mới
    if (!$cart_id) {
        $sql = "INSERT INTO Carts (user_id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_id = $stmt->insert_id;
        $stmt->close();
    }
}

// Xử lý yêu cầu "Thêm vào giỏ hàng"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        // Hiển thị modal nếu người dùng chưa đăng nhập
        echo "<script>document.addEventListener('DOMContentLoaded', function() { $('#loginModal').modal('show'); });</script>";
    } else {
        // Lấy ID sản phẩm từ yêu cầu POST
        $product_id = intval($_POST['product_id']);

        // Truy vấn thông tin sản phẩm từ cơ sở dữ liệu
        $sql = "SELECT price FROM Products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($price);
        $stmt->fetch();
        $stmt->close();

        // Nếu sản phẩm tồn tại trong cơ sở dữ liệu
        if ($price) {
            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $sql = "SELECT id, quantity, total_price FROM Cart_Items WHERE cart_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($item_id, $quantity, $item_total_price);
            $stmt->fetch();

            // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng và tổng giá
            if ($stmt->num_rows > 0) {
                $new_quantity = $quantity + 1;
                $new_total_price = $new_quantity * $price;

                $sql = "UPDATE Cart_Items SET quantity = ?, total_price = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("idi", $new_quantity, $new_total_price, $item_id);
                $stmt->execute();

                // Cập nhật tổng giá trong Carts
                $sql = "UPDATE Carts SET total_price = total_price + ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("di", $price, $cart_id);
                $stmt->execute();
            } else {
                // Nếu sản phẩm chưa tồn tại trong giỏ hàng, thêm mới sản phẩm
                $quantity = 1;
                $total_price = $price * $quantity;

                $sql = "INSERT INTO Cart_Items (cart_id, product_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiidd", $cart_id, $product_id, $quantity, $price, $total_price);
                $stmt->execute();

                // Cập nhật tổng giá trong Carts
                $sql = "UPDATE Carts SET total_price = total_price + ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("di", $price, $cart_id);
                $stmt->execute();
            }

            $stmt->close();
        }
    }
}

// Lấy các giá trị khác nhau của category từ bảng Products
$categories = [];
$sql = "SELECT DISTINCT category FROM Products";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Xử lý bộ lọc
$categoryFilter = isset($_GET['category']) ? explode(',', $_GET['category']) : [];
$priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

// Truy vấn dữ liệu sản phẩm từ bảng `Products` với các bộ lọc
$sql = "SELECT id, name, description, price, image_url, details_image, category, stock FROM Products WHERE 1=1";
if (!empty($categoryFilter)) {
    $categoryConditions = [];
    foreach ($categoryFilter as $category) {
        $categoryConditions[] = "category LIKE '%$category%'";
    }
    $categoryCondition = implode(' OR ', $categoryConditions);
    $sql .= " AND ($categoryCondition)";
}

if ($priceFilter) {
    if ($priceFilter == 'low_to_high') {
        $sql .= " ORDER BY price ASC";
    } else if ($priceFilter == 'high_to_low') {
        $sql .= " ORDER BY price DESC";
    }
}
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();


// Đặt URL cho các trang chính
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
    <title>Cửa hàng</title>
    <!-- Link đến Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link đến jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
                        <a class="nav-link" aria-current="page" href="<?php echo $homeURL; ?>">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $shopURL; ?>">Cửa hàng</a>
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
<div class="container mt-4">
    <h1 class="mb-4">Cửa hàng</h1>
    <!-- Bộ lọc sản phẩm -->
    <div class="row mb-4">
    <div class="row">
            <div class="col-md-3">
                <h5>Lọc sản phẩm</h5>
                <form method="GET" action="shop.php">
                    <div class="mb-3">
                        <label for="category" class="form-label">Phân loại túi</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Tất cả</option>
                            <option value="hot trend" <?php if(isset($_GET['category']) && $_GET['category'] == 'hot trend') echo 'selected'; ?>>Hot trend</option>
                            <option value="nu" <?php if(isset($_GET['category']) && $_GET['category'] == 'nu') echo 'selected'; ?>>Nữ</option>
                            <option value="tre em" <?php if(isset($_GET['category']) && $_GET['category'] == 'tre em') echo 'selected'; ?>>Trẻ em</option>
                            <option value="don gian" <?php if(isset($_GET['category']) && $_GET['category'] == 'don gian') echo 'selected'; ?>>Đơn giản</option>
                            <option value="dien loan" <?php if(isset($_GET['category']) && $_GET['category'] == 'dien loan') echo 'selected'; ?>>Điên loạn</option>
                            <option value="môi trường" <?php if(isset($_GET['category']) && $_GET['category'] == 'môi trường') echo 'selected'; ?>>Về môi trường</option>
                        </select>
                </div>
            <h5 class="mt-3">Lọc theo giá</h5>
            <div class="form-group">
                <select class="form-select" name="price">
                    <option value="">Chọn</option>
                    <option value="low_to_high" <?php if ($priceFilter == 'low_to_high') echo 'selected'; ?>>Từ thấp đến cao</option>
                    <option value="high_to_low" <?php if ($priceFilter == 'high_to_low') echo 'selected'; ?>>Từ cao đến thấp</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Lọc</button>
        </form>
    </div>
    <div class="col-md-9">
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card" data-id="<?php echo $row['id']; ?>">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title product-name"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text product-price"><?php echo number_format($row['price'], 0, ',', '.'); ?> VND</p>
                            <form method="POST" action="shop.php">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Thêm vào giỏ</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
</div>
<!-- Modal chi tiết sản phẩm -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h7 class="modal-title" id="productDetailModalLabel">Chi tiết sản phẩm</h7>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="product-detail-image" src="" class="img-fluid" alt="Product Image" style="width: 100%; height: auto; object-fit: contain;">
                    </div>
                    <div class="col-md-6">
                        <h5 id="product-detail-name"></h5>
                        <p id="product-detail-price"></p>
                        <p id="product-detail-description"></p>
                        <div class="d-flex justify-content-between">
                            <p id="product-detail-category"></p>
                            <p id="product-detail-stock"></p>
                        </div>
                        <button type="button" class="btn btn-primary" id="add-to-cart-btn">Thêm vào giỏ</button>
                        <div class="toast" id="addToCartToast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
                            <div class="toast-body">
                                Đã thêm vào giỏ hàng
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.product-card').click(function() {
        var productId = $(this).data('id');
        
        $.ajax({
            url: 'get_product_details.php',
            type: 'GET',
            data: { id: productId },
            success: function(response) {
                var product = JSON.parse(response);
                $('#product-detail-image').attr('src', product.details_image);
                $('#product-detail-name').text(product.name);
                $('#product-detail-price').text(product.price + ' VND');
                $('#product-detail-description').text(product.description);
                $('#product-detail-category').text('Phân loại: ' + product.category);
                $('#product-detail-stock').text('Kho: ' + product.stock);
                $('#productDetailModal').modal('show');
            }
        });
    });

    $('#add-to-cart-btn').click(function() {
            var productId = $('#product-detail-id').val();
            
            $.ajax({
                url: 'shop.php',
                type: 'POST',
                data: { add_to_cart: true, product_id: productId },
                success: function(response) {
                    // Hiển thị Toast thông báo thành công
                    $('.toast').toast('show');

                    // Tự động ẩn Toast sau 1 giây
                    setTimeout(function() {
                        $('.toast').toast('hide');
                    }, 1000);
                }
            });
        });
    });
</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>