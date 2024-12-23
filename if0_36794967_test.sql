-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql309.byetcluster.com
-- Generation Time: Jul 18, 2024 at 09:18 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_36794967_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `Carts`
--

CREATE TABLE `Carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Carts`
--

INSERT INTO `Carts` (`id`, `user_id`, `total_price`, `created_at`, `updated_at`) VALUES
(19, 16, '0.00', '2024-06-15 15:28:47', '2024-06-28 16:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `Cart_Items`
--

CREATE TABLE `Cart_Items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Feedback`
--

CREATE TABLE `Feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `Orders`
--

CREATE TABLE `Orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `payment_date` datetime DEFAULT NULL,
  `payment_method` enum('online','cod') NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_note` text DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `recipient_phone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`id`, `user_id`, `cart_id`, `order_date`, `payment_date`, `payment_method`, `total_price`, `order_note`, `shipping_address`, `recipient_name`, `recipient_phone`) VALUES
(36, 16, 19, '2024-06-28 12:41:13', NULL, 'cod', '305000.00', 'tests 123', '12312', 'test', '0123545679');

-- --------------------------------------------------------

--
-- Table structure for table `Order_Items`
--

CREATE TABLE `Order_Items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Order_Items`
--

INSERT INTO `Order_Items` (`id`, `order_id`, `product_id`, `quantity`, `total_price`, `created_at`, `updated_at`) VALUES
(31, 36, 6, 2, '150000.00', '2024-06-28 12:41:13', '2024-06-28 12:41:13'),
(32, 36, 7, 1, '75000.00', '2024-06-28 12:41:13', '2024-06-28 12:41:13'),
(33, 36, 19, 1, '80000.00', '2024-06-28 12:41:13', '2024-06-28 12:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `Products`
--

CREATE TABLE `Products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `details_image` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Products`
--

INSERT INTO `Products` (`id`, `name`, `description`, `price`, `stock`, `image_url`, `details_image`, `category`) VALUES
(6, 'Túi Save Earth, Save Yourself', 'Túi tote \"Save Earth, Save Yourself\" được làm từ vải canvas bền vững, với hình ảnh cây xanh và thông điệp mạnh mẽ về bảo vệ môi trường. Kích thước rộng rãi, thiết kế tiện dụng và quai xách chắc chắn, túi tote này là sự lựa chọn hoàn hảo cho những ai yêu thích phong cách sống xanh.', '75000.00', 10, 'images/bag1.png', 'detail_image/1.png', 'môi trường'),
(7, 'Túi Reduce, Reuse, Recycle ', 'Túi tote này là một lựa chọn hoàn hảo cho những ai quan tâm đến bảo vệ môi trường và thích phong cách bền vững. Với thiết kế đơn giản nhưng tinh tế, túi được in hình cây xanh và dòng chữ \"Reduce, Reuse, Recycle\", nhắc nhở mọi người về sự quan trọng của giảm thiểu, tái sử dụng và tái chế. Chất liệu vải bền bỉ và dễ dàng vệ sinh giúp túi trở thành một lựa chọn thân thiện với môi trường và thời trang. Mang túi này không chỉ là một phương tiện tiện dụng mà còn là một tuyên bố về lối sống bảo vệ môi trường, thúc đẩy ý thức bền vững và chia sẻ thông điệp tích cực đến mọi người xung quanh.', '75000.00', 10, 'images/bag2.png', 'detail_image/2.png', 'môi trường'),
(8, 'Túi Less Polution', 'Túi \"Less Pollution\" này là một sản phẩm không thể thiếu cho những ai quan tâm đến bảo vệ môi trường. Với hình ảnh cây xanh và thông điệp \"Reduce, Reuse, Recycle\", túi kết hợp giữa tính thực tiễn và phong cách bền vững. Chất liệu vải bền bỉ và dễ vệ sinh làm nổi bật tính thực dụng của sản phẩm, cùng lúc thúc đẩy những giá trị bảo vệ môi trường vào cuộc sống hàng ngày.', '75000.00', 10, 'images/bag3.png', 'detail_image/3.png', 'môi trường'),
(9, 'Túi Save Planet', 'Túi \"Save Planet\" là biểu tượng của sự cam kết trong việc bảo vệ hành tinh của chúng ta. Với hình ảnh minh họa cây xanh và thông điệp mạnh mẽ \"Reduce, Reuse, Recycle\", túi không chỉ là một sản phẩm thời trang mà còn là lời kêu gọi hành động. Chất liệu vải chắc chắn và thiết kế tiện lợi làm cho túi trở thành lựa chọn lý tưởng để mang theo hàng ngày, mang lại sự tiện ích và góp phần nhỏ vào việc giảm thiểu tác động xấu đến môi trường.', '75000.00', 10, 'images/bag4.png', 'detail_image/4.png', 'môi trường'),
(10, 'Túi Have A Good Day', 'Túi \"Have A Good Day\" là món đồ phụ kiện không thể thiếu để bắt đầu một ngày với nụ cười và cảm xúc tích cực. Với thiết kế đơn giản nhưng tinh tế và thông điệp đầy lạc quan, túi mang lại sự thoải mái và tiện dụng cho mọi hoạt động. Hãy để chiếc túi này trở thành người bạn đồng hành đầy ý nghĩa trong cuộc sống hàng ngày của bạn, làm bạn nhớ đến sự quý giá của mỗi khoảnh khắc và khuyến khích tư duy tích cực.', '75000.00', 10, 'images/bag5.png', 'detail_image/5.png', 'môi trường'),
(11, 'Túi Plant Vibes', 'Túi \"Plant Vibes\" là một sự lựa chọn thú vị cho những ai yêu thích sự gần gũi với thiên nhiên và mong muốn mang lại lợi ích cho môi trường. Với hình ảnh cây xanh và thông điệp bảo vệ môi trường rõ ràng, túi thể hiện sự tôn trọng và cam kết với việc giữ gìn hành tinh. Đây là một lựa chọn hoàn hảo cho những ai quan tâm đến bảo vệ môi trường và muốn lan tỏa những thông điệp tích cực về sự sống xanh đến mọi người xung quanh.', '75000.00', 10, 'images/bag6.png', 'detail_image/6.png', 'môi trường'),
(12, 'Túi Lucky Day', 'Túi \"Lucky Day\" mang đến một cảm giác may mắn và lạc quan với mẫu thiết kế đơn giản nhưng đầy ý nghĩa. Với hình ảnh may mắn và những lời chúc tốt đẹp, túi không chỉ là một phụ kiện thời trang mà còn là một lời nhắn về niềm vui và hy vọng. Đây là lựa chọn tuyệt vời để mang đến sự hạnh phúc và lưu giữ những kỷ niệm đáng nhớ trong mỗi ngày của bạn.', '75000.00', 10, 'images/bag7.png', 'detail_image/7.png', 'môi trường'),
(13, 'Túi Recycle', '\"Túi Recycle\" là một biểu tượng của sự tiếp thu và tái chế, với thiết kế đơn giản nhưng ý nghĩa sâu sắc. Với hình ảnh cây xanh và thông điệp \"Reduce, Reuse, Recycle\", túi không chỉ thể hiện sự cam kết của bạn đối với bảo vệ môi trường mà còn khuyến khích mọi người hành động bảo vệ hành tinh chung ta. Mỗi lần bạn sử dụng túi này là một bước tiến gần hơn đến một môi trường sạch hơn và bền vững hơn cho tương lai.', '75000.00', 10, 'images/bag8.png', 'detail_image/8.png', 'môi trường'),
(14, 'Túi Bướm Và Hoa', 'Mang đậm nét thiên nhiên và sự tươi mới, túi bướm và hoa là một sự kết hợp tinh tế giữa hình ảnh bướm mộng mơ và những đoá hoa rực rỡ. Với thông điệp về bảo vệ môi trường và sử dụng lại, túi này là lựa chọn hoàn hảo cho những ai yêu thích sự đơn giản và đồng thời muốn góp phần giảm thiểu lượng chất thải nhựa. Sự kết hợp hài hòa giữa thiết kế và thông điệp xanh tươi sẽ giúp bạn mang một phong cách bền vững vào mọi hoạt động hàng ngày.', '80000.00', 10, '/images/bag9.png', 'detail_image/9.png', 'màu sắc'),
(15, 'Túi Dâu Tây', 'Túi tote \"Dâu Tây\" là biểu tượng của sự tươi mới và màu sắc trong cuộc sống hàng ngày. Với hình ảnh dâu tây rực rỡ, túi không chỉ là một phụ kiện thời trang mà còn thể hiện sự yêu thích với thiên nhiên và sự sáng tạo. Chất liệu vải bền đẹp và thiết kế đơn giản, tiện lợi giúp túi trở thành bạn đồng hành đáng tin cậy cho mọi hoạt động. Đây là lựa chọn hoàn hảo không chỉ để mang theo món đồ cần thiết mà còn để thể hiện phong cách và sự quan tâm đến môi trường trong từng hành động của bạn.', '80000.00', 10, '/images/bag10.png', 'detail_image/10.png', 'màu sắc'),
(16, 'Túi Good Luck', 'Túi \"Good Luck\" là biểu tượng của sự may mắn và lời chúc tốt đẹp, được thể hiện qua thiết kế đơn giản nhưng đầy ý nghĩa. Với hình ảnh chữ \"Good Luck\" nổi bật, túi không chỉ là một phụ kiện thời trang mà còn là lời nhắc nhở về sự may mắn và niềm hy vọng trong cuộc sống hàng ngày. Chất liệu vải bền và thiết kế tiện lợi làm cho túi trở thành sự lựa chọn hoàn hảo để sử dụng mọi lúc, mang lại sự tiện ích và đồng thời hướng đến một phong cách sống thân thiện với môi trường.', '80000.00', 10, '/images/bag11.png', 'detail_image/11.png', 'màu sắc'),
(17, 'Túi Hoạt Hình', 'Túi tote \'Cô Gái Hoạt Hình\' là sự kết hợp tinh tế giữa phong cách và tính năng. Với họa tiết độc đáo của một cô gái hoạt hình, chiếc túi không chỉ là một phụ kiện thời trang mà còn là một tuyên bố cá nhân. Chất liệu vải bền bỉ và thiết kế rộng rãi giúp bạn dễ dàng đựng và mang theo đủ các vật dụng cần thiết hàng ngày. Sự kết hợp hài hòa giữa nghệ thuật và tiện ích, túi tote này sẽ là điểm nhấn độc đáo trong bộ sưu tập của bạn.', '80000.00', 10, '/images/bag12.png', 'detail_image/12.png', 'màu sắc'),
(18, 'Túi Hoa Màu Hồng', 'Túi Hoa  là một tác phẩm nghệ thuật di động, với họa tiết tươi sáng của hoa và bướm phản ánh sự thanh lịch và nữ tính. Thiết kế đơn giản nhưng tinh tế, kết hợp với chất liệu vải mềm mại và chắc chắn, mang lại sự thoải mái khi sử dụng hàng ngày. Đây là lựa chọn hoàn hảo để thể hiện phong cách cá nhân và làm nổi bật bất kỳ bộ trang phục nào', '80000.00', 10, '/images/bag13.png', 'detail_image/13.png', 'màu sắc'),
(19, 'Túi Photography', '\"Túi \'Photography\' là sự kết hợp hoàn hảo giữa phong cách và tính năng. Với thiết kế in hình các biểu tượng của nghệ thuật chụp ảnh, từ máy ảnh cổ điển đến các chi tiết nhỏ như phấn nụ, chiếc túi không chỉ là một phụ kiện thời trang mà còn là một cách để biểu thị đam mê nghệ thuật của bạn. Chất liệu vải cao cấp và đường may tỉ mỉ giúp túi có độ bền cao và sử dụng lâu dài. Với túi \'Photography\', bạn không chỉ mang theo một phụ kiện, mà còn là một tuyên bố về sự đam mê và cá tính riêng của bạn.\"', '80000.00', 10, '/images/bag14.png', 'detail_image/14.png', 'màu sắc'),
(20, 'Túi Các Bức Ảnh Về Hoa', '\"Túi \'Các Bức Ảnh Về Hoa\' là một sự kết hợp tinh tế giữa nghệ thuật và thiết kế hiện đại. Với họa tiết in các bức ảnh đẹp về hoa, từ hoa hồng tinh khôi đến hoa tulip nổi bật, chiếc túi mang đến một cái nhìn tươi mới và sự thanh lịch cho người sử dụng. Chất liệu vải bền bỉ và đường may tỉ mỉ, túi không chỉ là một phụ kiện thời trang mà còn là một tuyên bố về sự đẹp và sự quý giá của thiên nhiên. Đây là lựa chọn hoàn hảo để thể hiện sự yêu thích và sự kết nối với vẻ đẹp tự nhiên.\"', '80000.00', 10, '/images/bag15.png', 'detail_image/15.png', 'màu sắc'),
(21, 'Túi Playful Things', ' \"Túi \'Playful Things\' là biểu tượng của sự vui nhộn và sáng tạo. Với thiết kế mô phỏng hình ảnh các đồ vật đáng yêu và hài hước như đồ chơi, chiếc túi không chỉ là một phụ kiện thời trang mà còn là một cách để thể hiện cá tính và sự hồn nhiên của bạn. Chất liệu vải mềm mại và đường may tỉ mỉ giúp túi có độ bền cao và sử dụng lâu dài. Với túi \'Playful Things\', bạn sẽ luôn mang theo một món đồ mang tính cảm hứng và nổi bật trong mọi hoạt động hàng ngày của mình.\"', '80000.00', 10, '/images/bag16.png', 'detail_image/16.png', 'màu sắc'),
(22, 'Túi Những Bức Ảnh Du Lịch', '\"Túi \'Những Bức Ảnh Du Lịch\' là sự kết hợp hoàn hảo giữa phong cách và kỷ niệm du lịch. Với thiết kế in các bức ảnh tuyệt đẹp về các điểm đến nổi tiếng như Paris, Tokyo, và New York, chiếc túi không chỉ là một phụ kiện thời trang mà còn là một cách để tái hiện lại những kỷ niệm đáng nhớ của bạn. Chất liệu vải cao cấp và đường may tỉ mỉ giúp túi có độ bền cao và sử dụng lâu dài. Với túi \'Những Bức Ảnh Du Lịch\', bạn có thể mang theo một phần của thế giới mà bạn đã khám phá và chia sẻ cảm xúc đó với mọi người xung quanh.\"', '80000.00', 10, '/images/bag17.png', 'detail_image/17.png', 'màu sắc'),
(23, 'Túi Good Vibe Only', '\"Túi \'Good Vibe Only\' là biểu tượng của sự tích cực và lối sống lạc quan. Với thông điệp rõ ràng và phác thảo đơn giản, chiếc túi không chỉ là một phụ kiện thời trang mà còn là lời nhắc nhở về việc giữ cho cuộc sống luôn tràn đầy những cảm xúc tích cực. Chất liệu vải chắc chắn và thiết kế đơn giản nhưng hiện đại làm cho túi trở thành lựa chọn hoàn hảo để mang đi hằng ngày, mang đến sự tiện lợi và phong cách cho người sử dụng.\"', '80000.00', 10, '/images/bag18.png', 'detail_image/18.png', 'màu sắc'),
(24, 'Túi Retro Time', '\"Túi \'Retro Time\' là sự kết hợp hài hòa giữa phóng khoáng và phong cách cổ điển. Với thiết kế mang đậm nét hoài cổ, từ họa tiết retro đến gam màu đậm chất thập niên 80, chiếc túi không chỉ là một phụ kiện thời trang mà còn là một món đồ để tái hiện lại phong cách thời trang của quá khứ. Chất liệu vải bền bỉ và thiết kế tiện dụng giúp túi trở thành một sự lựa chọn tuyệt vời để thể hiện sự cá tính và đam mê với thời trang retro của bạn.\"', '80000.00', 10, '/images/bag19.png', 'detail_image/19.png', 'màu sắc'),
(25, 'Túi Chanh Dây', '\"Túi \'Chanh Dây\' là biểu tượng của sự tươi mới và đầy sức sống. Với họa tiết và màu sắc tươi sáng, như một biểu tượng của loại trái cây tươi ngon, chiếc túi không chỉ là một phụ kiện thời trang mà còn là một lựa chọn lý tưởng để mang theo mỗi ngày. Chất liệu vải mềm mại và thiết kế đơn giản nhưng thu hút, giúp túi \'Chanh Dây\' mang đến sự tươi mới và năng động cho phong cách của bạn.\"', '80000.00', 10, '/images/bag20.png', 'detail_image/20.png', 'màu sắc');

-- --------------------------------------------------------

--
-- Table structure for table `Product_Categories`
--

CREATE TABLE `Product_Categories` (
  `product_id` int(11) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`id`, `username`, `password`, `email`, `phone_number`, `address`, `otp_code`, `otp_expiry`, `is_verified`) VALUES
(16, 'test1', '05a671c66aefea124cc08b76ea6d30bb', 'test1@test.com', '0946352723', '109 Ton Duc Thang, Tam Ky, Quang Nam', NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Carts`
--
ALTER TABLE `Carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Cart_Items`
--
ALTER TABLE `Cart_Items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cart_id` (`cart_id`);

--
-- Indexes for table `Order_Items`
--
ALTER TABLE `Order_Items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Product_Categories`
--
ALTER TABLE `Product_Categories`
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Carts`
--
ALTER TABLE `Carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `Cart_Items`
--
ALTER TABLE `Cart_Items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `Feedback`
--
ALTER TABLE `Feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `Order_Items`
--
ALTER TABLE `Order_Items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `Products`
--
ALTER TABLE `Products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Carts`
--
ALTER TABLE `Carts`
  ADD CONSTRAINT `Carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);

--
-- Constraints for table `Cart_Items`
--
ALTER TABLE `Cart_Items`
  ADD CONSTRAINT `Cart_Items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `Carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Cart_Items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Orders`
--
ALTER TABLE `Orders`
  ADD CONSTRAINT `Orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `Orders_ibfk_2` FOREIGN KEY (`cart_id`) REFERENCES `Carts` (`id`);

--
-- Constraints for table `Order_Items`
--
ALTER TABLE `Order_Items`
  ADD CONSTRAINT `Order_Items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`id`),
  ADD CONSTRAINT `Order_Items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`);

--
-- Constraints for table `Product_Categories`
--
ALTER TABLE `Product_Categories`
  ADD CONSTRAINT `Product_Categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
