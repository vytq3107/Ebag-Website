<?php
// Bắt đầu phiên làm việc
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "admin";
$password = "taphoahungvy@#.com";
$dbname = "test";

// Tạo kết nối cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Hàm mã hóa mật khẩu
function hashPassword($password) {
    // Bạn có thể sử dụng các phương pháp mã hóa mật khẩu như password_hash() trong PHP
    // Dưới đây là một ví dụ cơ bản, tuy nhiên nên sử dụng các hàm mã hóa mật khẩu an toàn hơn trong thực tế
    return md5($password); // Ví dụ sử dụng md5, nhưng không được khuyến khích trong sản phẩm thực tế
}

// Xử lý biểu mẫu được gửi đi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form_type'])) {
        $form_type = $_POST['form_type'];

        if ($form_type === 'login') {
            // Xử lý đăng nhập
            handle_login($conn);
        } elseif ($form_type === 'signup') {
            // Xử lý đăng ký
            handle_signup($conn);
        }
    }
}
// Hàm xử lý đăng nhập (login)
function handle_login($conn) {
    // Nhận thông tin từ form đăng nhập
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Truy vấn cơ sở dữ liệu để kiểm tra thông tin đăng nhập
    $sql = "SELECT * FROM Users WHERE email = ?";

    $sql = "SELECT * FROM table ";$sql = "SELECT * FROM Users WHERE email = test";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra kết quả truy vấn
    if ($result->num_rows > 0) {
        // Lấy thông tin người dùng
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (hashPassword($password) === $user['password']) {
            // Đăng nhập thành công
            // Tạo phiên làm việc và lưu trữ thông tin người dùng
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            
            // Chuyển hướng đến trang index.php
            header("Location: index.php");
            exit();
        } else {
            // Sai mật khẩu
            echo "Sai mật khẩu. Vui lòng thử lại.";
        }
    } else {
        // Sai email hoặc không tìm thấy người dùng
        echo "Email không tồn tại. Vui lòng thử lại.";
    }

    // Đóng kết nối cơ sở dữ liệu
    $stmt->close();
}

// Hàm xử lý đăng ký (signup)
function handle_signup($conn) {
    // Nhận thông tin từ form đăng ký
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Kiểm tra độ dài mật khẩu phải từ 8 ký tự trở lên
    if (strlen($password) < 8) {
        echo "Mật khẩu phải từ 8 ký tự trở lên. Vui lòng thử lại.";
        return;
    }

    // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
    $hashed_password = hashPassword($password);

    // Kiểm tra xem email đã tồn tại trong cơ sở dữ liệu chưa
    $sql_check_email = "SELECT * FROM Users WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();

    if ($result_check_email->num_rows > 0) {
        // Email đã tồn tại, thông báo lỗi
        echo "Email đã tồn tại. Vui lòng thử lại.";
    } else {
        // Lưu thông tin đăng ký vào cơ sở dữ liệu
        $sql = "INSERT INTO Users (username, email, password, phone_number, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $phone, $address);

        if ($stmt->execute()) {
            // Đăng ký thành công
            echo "Đăng ký thành công! Vui lòng đăng nhập.";
        } else {
            // Lỗi khi đăng ký
            echo "Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.";
        }
    }

    // Đóng kết nối cơ sở dữ liệu
    $stmt->close();
    $stmt_check_email->close();
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style_login.css">
    <title>Login and Sign up</title>
</head>

<body>

    <div class="container" id="container">
        <!-- Form đăng ký (signup) -->
        <div class="form-container sign-up">
            <form action="login.php" method="POST">
                <h1>Tạo tài khoản</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>Hoặc sử dụng email của bạn để đăng ký</span>
                <input type="hidden" name="form_type" value="signup"> <!-- Trường ẩn để xác định loại biểu mẫu -->
                <input type="text" name="username" placeholder="Tài khoản" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu (tối thiểu 8 ký tự)" minlength="8" required>
                <input type="text" name="phone" placeholder="Số điện thoại" required>
                <input type="text" name="address" placeholder="Địa chỉ" required>
                <button type="submit">Đăng ký</button>
            </form>
        </div>
        <!-- Form đăng nhập (login) -->
        <div class="form-container sign-in">
            <form action="login.php" method="POST">
                <h1>Đăng nhập</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>hoặc sử dụng email để đăng nhập</span>
                <input type="hidden" name="form_type" value="login"> <!-- Trường ẩn để xác định loại biểu mẫu -->
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <a href="#">Quên mật khẩu?</a>
                <button type="submit">Đăng nhập</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Chào mừng!</h1>
                    <p>Đăng nhập để mua sắm</p>
                    <button class="hidden" id="login">Đăng nhập</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Xin chào!</h1>
                    <p>Đăng ký để truy cập website</p>
                    <button class="hidden" id="register">Đăng ký</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');

        // Sự kiện cho nút đăng ký
        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        // Sự kiện cho nút đăng nhập
        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });
    </script>
</body>

</html>
