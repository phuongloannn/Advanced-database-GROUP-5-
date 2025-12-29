-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 02, 2025 lúc 05:39 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `fashion_shop_group5`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `img` varchar(100) NOT NULL,
  `small_content` mediumtext DEFAULT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `image` varchar(191) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`, `image`, `created_at`) VALUES
(22, 'Xe đạp địa hình', 'xe-dap-dia-hinh', 'Với xu hướng phát triển bận rộn như ngày nay, mọi người thường làm việc văn phòng công sở không có thời gian hoạt động, để nâng cao sức khỏe và để phòng tránh được những bệnh tật. Bộ môn đạp xe đạp rất được nhiều người quan tâm nhất là ở các thành phố lớn, đạp xe đạp nâng cao sức khỏe và có rất nhiều tác dụng tốt cho cơ thể, thứ nhất là bộ môn bơi lội, thứ nhì là đạp xe đạp. ', 0, '1683353162.webp', '2023-05-06 06:03:38'),
(23, 'Xe đạp trẻ em', 'xe-dap-tre-em-48', 'Xe đạp trẻ em Somings Lion Bird Boulder là mẫu xe đạp cực hot đang được các bậc phụ huynh săn đón cho các bé. Xe sở hữu bộ khung sườn làm từ hợp kim nhôm siêu nhẹ phủ lớp Sơn chống độc không mùi, chất lượng đến từ cao su cao cấp – chứng nhận “sơn chống độc” do công ty SGS – Thụy Sĩ cung cấp. Với kích thước bánh 20 inch phù hợp cho các bé trong độ tuổi 7 - 9 tuổi với chiều cao từ 1m3 trở lên.', 0, '1683353143.webp', '2023-05-06 06:05:43'),
(24, 'Xe đạp tuaring', 'xe-dap-tuaring-98', 'Xe đạp đường phố Somings Traverse Pro với thiết kế nữ tính, kiểu dáng đẹp, được khách hàng đánh giá là chiếc xe đạp phong cách khác biệt, phù hợp với dân văn phòng, học sinh, sinh viên hay giới nội trợ. Sở hữu 3 phối màu hiện đại kết hợp cùng kiểu dáng nữ tính, xe rất được lòng khách hàng nữ.', 0, '1683353648.webp', '2023-05-06 06:14:08'),
(25, 'Xe đạp đua', 'xe-dap-dua-28', 'Xe đạp đua Maruishi AIR FORCE là mẫu xe đạp đua cao cấp của hãng xe đạp thể thao Maruishi, mang phong cách thiết kế thể thao, cấu hình xe và độ bền cực cao. Mẫu xe đạp này có thể đáp ứng tốt nhu cầu của người sử dụng, vận hành êm ái ở tốc độ cao và ổn định trên những hành trình dài.', 0, '1683353727.webp', '2023-05-06 06:15:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `guest_name` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'cod'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `created_at`, `guest_name`, `payment_method`) VALUES
(16, 50, 2, '2025-05-17 01:44:04', NULL, 'cod'),
(17, 48, 2, '2025-05-27 18:58:59', NULL, 'cod'),
(18, 48, 0, '2025-05-27 19:00:17', NULL, 'cod'),
(19, 0, 0, '2025-05-27 19:03:32', 'thế', 'cod'),
(22, 0, 0, '2025-05-27 19:09:28', 'thế', 'cod'),
(23, 0, 4, '2025-05-27 19:10:20', 'Hiệ', 'cod'),
(25, 50, 1, '2025-05-27 19:12:49', NULL, 'cod'),
(48, 55, 2, '2025-05-31 17:57:37', NULL, 'cod'),
(49, 55, 0, '2025-06-01 11:36:00', NULL, 'cod'),
(50, 55, 0, '2025-06-01 11:36:11', NULL, 'cod'),
(51, 55, 0, '2025-06-01 11:36:41', NULL, 'cod'),
(52, 55, 0, '2025-06-01 11:37:31', NULL, 'cod'),
(54, 55, 0, '2025-06-01 11:40:12', NULL, 'vnpay'),
(55, 55, 0, '2025-06-01 11:41:03', NULL, 'cod'),
(56, 55, 0, '2025-06-01 11:41:45', NULL, 'vnpay'),
(57, 55, 0, '2025-06-01 11:42:57', NULL, 'vnpay'),
(58, 55, 0, '2025-06-01 11:45:48', NULL, 'vnpay'),
(59, 55, 0, '2025-06-01 11:46:50', NULL, 'vnpay'),
(60, 55, 0, '2025-06-01 11:48:44', NULL, 'vnpay'),
(61, 55, 0, '2025-06-01 11:49:19', NULL, 'vnpay'),
(62, 55, 0, '2025-06-01 11:50:49', NULL, 'vnpay'),
(63, 55, 0, '2025-06-01 11:54:50', NULL, 'vnpay'),
(64, 55, 0, '2025-06-01 11:56:28', NULL, 'cod'),
(65, 55, 0, '2025-06-01 11:57:04', NULL, 'cod'),
(67, 55, 0, '2025-06-01 11:58:33', NULL, 'vnpay'),
(68, 55, 0, '2025-06-01 12:07:22', NULL, 'cod'),
(72, 55, 1, '2025-06-01 12:12:21', NULL, 'cod');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_detail`
--

CREATE TABLE `order_detail` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `selling_price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `rate` tinyint(4) DEFAULT NULL,
  `comment` mediumtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_detail`
--

INSERT INTO `order_detail` (`id`, `user_id`, `product_id`, `order_id`, `selling_price`, `quantity`, `status`, `rate`, `comment`, `created_at`) VALUES
(47, 49, 45, NULL, 1200, 1, 1, NULL, NULL, '2023-05-06 07:17:51'),
(48, 50, 45, 16, 120000, 15, 2, NULL, NULL, '2025-05-17 01:43:52'),
(54, NULL, 52, 22, 140, 1, 1, NULL, NULL, '2025-05-27 19:09:28'),
(55, NULL, 48, 23, 275, 1, 1, NULL, NULL, '2025-05-27 19:10:20'),
(57, NULL, 48, 25, 275, 1, 1, NULL, NULL, '2025-05-27 19:12:49'),
(58, NULL, 47, 25, 3000, 1, 1, NULL, NULL, '2025-05-27 19:12:49'),
(59, NULL, 54, 25, 100, 1, 1, NULL, NULL, '2025-05-27 19:12:49'),
(65, NULL, 55, 25, 220, 3, 1, NULL, NULL, '2025-05-28 06:11:00'),
(100, 55, 64, 48, 1500, 1, 2, NULL, NULL, '2025-05-31 17:57:26'),
(104, NULL, 74, 55, 600, 40, 1, NULL, NULL, '2025-06-01 11:41:03'),
(105, NULL, 70, 56, 6000, 40, 1, NULL, NULL, '2025-06-01 11:41:45'),
(106, NULL, 70, 57, 6000, 40, 1, NULL, NULL, '2025-06-01 11:42:57'),
(107, NULL, 70, 58, 6000, 1, 1, NULL, NULL, '2025-06-01 11:45:48'),
(108, NULL, 70, 59, 6000, 1, 1, NULL, NULL, '2025-06-01 11:46:50'),
(109, 55, 47, 64, 3000, 1, 2, NULL, NULL, '2025-06-01 11:55:21'),
(110, 55, 47, 65, 3000, 1, 2, NULL, NULL, '2025-06-01 11:56:45'),
(115, 55, 72, 72, 300, 14, 2, NULL, NULL, '2025-06-01 12:12:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `small_description` mediumtext NOT NULL,
  `description` mediumtext NOT NULL,
  `original_price` int(11) NOT NULL,
  `selling_price` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `small_description`, `description`, `original_price`, `selling_price`, `qty`, `status`, `created_at`, `image`, `image1`, `image2`, `image3`) VALUES
(45, 25, 'Xe đạp đua RIKULAU CADENCE', 'xe-dap-dua-rikulau-cadence-63', 'Thương hiệu: Rikulau\r\nChất liệu khung: Hợp kim nhôm\r\nLoại phanh: Phanh đĩa cơ Tektro\r\nPhuộc giảm xóc: Hợp kim nhôm\r\nLíp: SRAM APEX 11S\r\nGiò đĩa: SRAM APEX 42T\r\nTay đề: SRAM APEX 1x11S\r\nĐề trước: Không\r\nĐề sau: SRAM APEX 11S\r\nTốc độ: 11\r\nVành xe: Hợp kim nhôm 2 lớp\r\nKích cỡ bánh xe: 700\r\nLốp xe: Bám đường, chống trượt\r\nDây âm sườn: Có\r\nGấp gọn: Không', 'Xe đạp đua RIKULAU CADENCE mang trong mình kiểu dáng thể thao với thiết kế hiện đại, thời thượng, phù hợp với những hoạt động ngoài trời nhằm mục đích rèn luyện sức khỏe hay chinh phục những cung đường đầy thử thách.', 1247, 1200, 9, 0, '2023-05-06 06:26:51', 'dua1.jpg', 'dua2-1.jpg', 'dua2-2.jpg', 'dua2-3.jpg'),
(47, 25, 'Xe đạp đua RIKULAU ILI ILI', 'xe-dap-dua-rikulau-ili-ili-30', 'Thương hiệu: Rikulau\r\nChất liệu khung: Thép không gỉ\r\nLoại phanh: Phanh đĩa cơ TEKTRO\r\nPhuộc giảm xóc: Sợi carbon\r\nLíp: SRAM PG 1130, 11-30T, 11S\r\nGiò đĩa: SRAM APEX 1,42T*170L\r\nTay đề: SRAM APEX 1, 1x11S\r\nĐề trước: Không\r\nĐề sau: SRAM APEX 1 11S\r\nTốc độ: 11\r\nVành xe: Hợp kim nhôm 2 lớp\r\nKích cỡ bánh xe: 700\r\nLốp xe: Schwalbe G-One 700x38C\r\nDây âm sườn: Có\r\nGấp gọn: Không', 'Xe đạp đua RIKULAU ILI ILI đến từ thị trường Đài Loan vô cùng khó tính sở hữu thiết kế vô cùng ấn tượng với kiểu dáng thể thao, khỏe khoắn thích hợp với người dùng có nhu cầu đi lại hằng ngày, đi phượt. Mẫu xe ra đời cùng với bộ khung thép, hệ thống truyền động 11 tốc độ.', 3043, 3000, 7, 0, '2023-05-06 06:33:43', 'dua3.jpg', 'dua1-1.jpg', 'dua1-2.jpg', 'dua1-3.jpg'),
(48, 24, 'Xe đạp touring Maruishi BALBOA', 'xe-dap-touring-maruishi-balboa-51', 'Thương hiệu: Maruishi \r\nChất liệu khung: Hợp kim nhôm 6061\r\nLoại phanh: Phanh vành\r\nPhuộc giảm xóc: Hợp kim nhôm 6061\r\nLíp: Shimano TZ500 7 tầng\r\nGiò đĩa: Prowheel 48T\r\nTay đề: SHIMANO TOURNEY 1x7S\r\nĐề trước: Không\r\nĐề sau: SHIMANO TOURNEY 7S\r\nTốc độ: 7\r\nVành xe: Hợp Kim Nhôm 2 lớp\r\nKích cỡ bánh xe: 700\r\nLốp xe: Kenda Kwest\r\nDây âm sườn: Không\r\nGấp gọn: Không', 'Chưa cập nhật', 300, 275, 5, 0, '2023-05-06 06:37:18', 'tua1.jpg', 'tua1-1.jpg', 'tua1-2.jpg', 'tua1-3.jpg'),
(49, 24, 'Xe đạp touring RIKULAU Traverse 700C', 'xe-dap-touring-rikulau-traverse-700c-92', 'Thương hiệu: Rikulau\r\nChất liệu khung: Hợp kim thép\r\nLoại phanh: Phanh vành\r\nPhuộc giảm xóc: Hợp kim thép\r\nLíp: Shimano Tz500- 7S – 14/28T\r\nGiò đĩa: Hợp kim nhôm 48T\r\nTay đề: Shimano TX30 1x7S\r\nĐề trước: Không\r\nĐề sau: Shimano Tourney TY21 7S\r\nTốc độ: 7\r\nVành xe: Hợp kim nhôm 2 lớp\r\nKích cỡ bánh xe: 700\r\nLốp xe: Kenda 700x28C\r\nDây âm sườn: Không\r\nGấp gọn: Không', 'Xe đạp touring RIKULAU Traverse 700C có vẻ ngoài thanh lịch, đầy quyến rũ của một mẫu xe đường phố. Xe sở hữu ngoại hình cổ điển phong cách phù hợp với người giới có chiều cao từ 1m6 trở lên. Với nhiều mầu sắc thời trang, độc đáo để khách hàng lựa chọn, chắc chắn đây là mẫu xe mà khách hàng không thể bỏ lỡ.', 217, 200, 9, 0, '2023-05-06 06:50:36', 'tua2.jpg', 'tua2-1.jpg', 'tua2-2.jpg', 'tua2-3.jpg'),
(51, 24, 'Xe đạp touring Maruishi Half Miler', 'xe-dap-touring-maruishi-half-miler-71', 'Thương hiệu: Maruishi\r\nChất liệu khung: Hợp kim nhôm 6061\r\nLoại phanh: Phanh vành\r\nPhuộc giảm xóc: Hợp kim nhôm 6061\r\nLíp: Shimano 7 tầng\r\nGiò đĩa: Hợp kim nhôm\r\nTay đề: Shimano 1x7S\r\nĐề trước: Không\r\nĐề sau: Shimano 7S\r\nTốc độ: 7\r\nVành xe: Hợp kim nhôm 2 lớp\r\nKích cỡ bánh xe: 700\r\nLốp xe: 700x32C\r\nDây âm sườn: Không\r\nGấp gọn: Không', 'Bạn đang cần tìm một chiếc xe nhẹ nhàng, thanh lịch, êm ái nhưng vẫn ổn định trên mọi cung đường, Mẫu xe Maruishi Half Miler đến từ Nhật Bản sẽ là chiếc xe khiến bạn hài lòng ngay từ cái nhìn đầu tiên. Xe đạp touring Maruishi Half Miler là chiếc xe đạp thành phố, với kiểu dáng trang nhã, màu sắc khỏe khoắn, phù hợp cho nhiều đối tượng sử dụng, từ thanh niên đến trung niên. Với nhiều mục đích sử dụng, từ đi làm, đi tập thể dục, đi chơi, đi phượt….', 434, 400, 10, 0, '2023-05-06 06:54:45', 'tua3.jpg', 'tua3-1.jpg', 'tua3-2.jpg', 'tua3-3.jpg'),
(52, 23, 'Xe đạp trẻ em NISHIKI ALADIN 16', 'xe-dap-tre-em-nishiki-aladin-16-73', 'Thương hiệu: NISHIKI\r\nChất liệu khung: Hợp kim nhôm\r\nLoại phanh: Phanh đĩa cơ\r\nPhuộc giảm xóc: Hợp kim nhôm\r\nTốc độ: 1\r\nKích cỡ bánh xe: 16\"\r\nLốp xe: Bám đường, chống trượt\r\nDây âm sườn: Không\r\nGấp gọn: Không', 'Xe đạp trẻ em NISHIKI ALADIN 16 sở hữu vẻ ngoài thể thao với bánh 16 inch là sự lựa chọn tuyệt vời cho những bạn nhỏ thích một mẫu xe thể thao với thiết kế vượt trội. Bộ cấu hình cao cấp bao gồm khung nhôm siêu nhẹ, phanh đĩa cơ, tổng thể sẽ tạo nên một chiếc xe cực kỳ hài hòa và đẳng cấp, mang đến cho các bạn nhỏ 1 chiếc xe đạp tuyệt vời để đi học, đi chơi, hay đi thể dục thể thao giúp tăng cường trí lực và sức lực.', 160, 140, 8, 0, '2023-05-06 07:00:01', 'kid1.jpg', 'kid1-1.jpg', 'kid1-2.jpg', 'kid1-3.jpg'),
(53, 23, 'Xe đạp trẻ em NISHIKI ANNA 20', 'xe-dap-tre-em-nishiki-anna-20-24', 'Thương hiệu: NISHIKI\r\nChất liệu khung: Hợp kim thép\r\nLoại phanh: Phanh V trước, bát sau\r\nPhuộc giảm xóc: Hợp kim nhôm\r\nTốc độ: 1\r\nVành xe: Vành nan hoa\r\nKích cỡ bánh xe: 20\"\r\nLốp xe: Bám đường, chống trượt\r\nDây âm sườn: Không\r\nGấp gọn: Không', 'Ngay lần đầu nhìn thấy xe đạp trẻ em NISHIKI ANNA 20, các bé và phụ huynh sẽ rất ấn tượng với thiết kế thời trang thể thao với 3 phối màu hiện đại cùng kích cỡ bánh 20 inch. Đây sẽ là lựa chọn tuyệt vời dành cho các bé trong giai đoạn phát triển và tập đi xe.', 156, 120, 9, 0, '2023-05-06 07:02:09', 'kid2.jpg', 'kid2-1.jpg', 'kid2-2.jpg', 'kid2-3.jpg'),
(54, 23, 'Xe đạp trẻ em NISHIKI ELSA 16', 'xe-dap-tre-em-nishiki-elsa-16-26', 'Thương hiệu: NISHIKI\r\nChất liệu khung: Hợp kim thép\r\nLoại phanh: Phanh V trước, bát sau\r\nPhuộc giảm xóc: Hợp kim thép\r\nTốc độ: 1\r\nVành xe: Vành nan hoa\r\nKích cỡ bánh xe: 16\"\r\nLốp xe: 16x1.75\"\r\nDây âm sườn: Không\r\nGấp gọn: Không', 'Xe đạp trẻ em NISHIKI ELSA 16 của hãng xe đạp NISHIKI nổi tiếng của Nhật Bản. Xe được thiết kế theo phong cách hiện đại nhưng vô cùng bắt mắt đáng yêu với ba mầu Hồng, Đỏ, Vàng. Đây sẽ là lựa chọn tuyệt vời dành cho các bé yêu thích đạp xe dạo chơi hằng ngày.', 117, 100, 8, 0, '2023-05-06 07:03:36', 'kid3.jpg', 'kid3-1.jpg', 'kid3-2.jpg', 'kid3-3.jpg'),
(55, 22, 'Xe đạp địa hình thể thao NISHIKI X1', 'xe-dap-dia-hinh-the-thao-nishiki-x1-32', 'Thương hiệu: NISHIKI\r\nChất liệu khung: Nhôm Aluminium 6061\r\nLoại phanh: Phanh đĩa cơ\r\nPhuộc giảm xóc: Lò xo có khóa hành trình\r\nLíp: ATa 11/32T 7 Speed\r\nGiò đĩa: Prowheel 3 tầng 22/34/44T\r\nTay đề: Shimano Tourney 3x7S\r\nĐề trước: Shimano Tourney 3S\r\nĐề sau: Shimano Tourney 7S\r\nTốc độ: 21\r\nVành xe: Hợp Kim Nhôm 2 lớp\r\nKích cỡ bánh xe: 27.5\"\r\nLốp xe: 27,5 X 1.95\"\r\nDây âm sườn: Có\r\nGấp gọn: Không', 'Xe đạp địa hình thể thao NISHIKI X1 có vẻ ngoài cá tính, khỏe khoắn cùng với logo NISHIKI sành điệu, đẹp mắt, phù hợp với người dùng có nhu cầu rèn luyện sức khỏe, yêu thích bộ môn đạp xe leo núi hay muốn dùng xe đạp để làm phương tiện để đi học, đi làm. Mẫu xe này được cách tân với xu thế tiên tiến, ngày càng đáp ứng những thị trường khắt khe nhất không chỉ tại Nhật Bản mà còn các nước khác.', 239, 220, 5, 0, '2023-05-06 07:06:54', 'diahinh1.jpg', 'diahinh1-1.jpg', 'diahinh1-2.jpg', 'diahinh1-3.jpg'),
(60, 22, 'Xe đạp địa hình thể thao RIKULAU U27', 'xe-dap-dia-hinh-the-thao-rikulau-u27-71', 'Thương hiệu: Rikulau\r\nChất liệu khung: Hợp kim nhôm 6061\r\nLoại phanh: Phanh đĩa cơ\r\nPhuộc giảm xóc: Giảm xóc nhôm\r\nLíp: SHIMANO 8 tầng\r\nGiò đĩa: Hợp kim 24/34/42T\r\nTay đề: Shimano 3x8S\r\nĐề trước: SHIMANO TOURNEY TY 3S\r\nĐề sau: SHIMANO TOURNEY TY 8S\r\nTốc độ: 24\r\nVành xe: Hợp kim nhôm 2 lớp\r\nKích cỡ bánh xe: 27.5\"\r\nLốp xe: CST 27.5x2.1\"\r\nDây âm sườn: Có\r\nGấp gọn: Không', 'Với thiết kế trẻ trung cùng phối màu nổi bật và những ưu thế vượt trội của thương hiệu Đài Loan, xe đạp địa hình RIKULAU U27 là một sự lựa chọn tuyệt vời cho các hoạt động ngoài trời, đặc biệt là các hoạt động chinh phục, khám phá những địa hình mới.', 265, 250, 6, 0, '2023-05-06 07:12:43', 'diahinh2.jpg', 'diahinh2-1.jpg', 'diahinh2-2.jpg', 'diahinh2-3.jpg'),
(61, 22, 'Xe đạp địa hình thể thao Maruishi ASO', 'xe-dap-dia-hinh-the-thao-maruishi-aso-68', 'Thương hiệu: Maruishi\r\nChất liệu khung: Hợp kim nhôm 6061\r\nLoại phanh: Phanh đĩa cơ 160mm\r\nPhuộc giảm xóc: Phuộc dầu có khóa hành trình nhún 100mm \r\nLíp: SHIMANO TZ21-7S,14-28T\r\nGiò đĩa: Prowheel 24/34/42T\r\nTay đề: Maruishi A3 3x8s\r\nTốc độ: 24\r\nVành xe: Hợp kim nhôm 6061\r\nKích cỡ bánh xe: 26\"\r\nLốp xe: CST JET 26 x1.95\"\r\nDây âm sườn: Có\r\nGấp gọn: Không', 'Xe đạp địa hình Maruishi ASO sở hữu ngoại hình khỏe khoắn, mạnh mẽ cùng nhiều phiên bản màu sắc để bạn lựa chọn phù hợp với sở thích. Mẫu xe này phù hợp những ai đam mê trải nghiệm và khám phá các cung đường mới, hay đơn giản là đi học, đi làm.', 300, 250, 8, 0, '2023-05-06 07:14:33', 'diahinh3.jpg', 'diahinh3-1.jpg', 'diahinh3-2.jpg', 'diahinh3-3.jpg'),
(64, 24, 'xe đạp java', 'xedajava', '', '', 1000, 1500, 199, 0, '2025-05-29 17:31:23', '1748539883_xe_java.jfif', '1748539883_xe_java1_1.jfif', '1748539883_xe_java_1_2.webp', '1748539883_xe_java_1_3.jfif'),
(69, 23, 'xe máy', 'xemay', '', '', 1000, 2000, 999, 0, '2025-05-30 04:28:57', '1748579337_xetream3.jpg', '1748579337_1748539883_xe_java_1_2.webp', '1748579337_1748539883_xe_java_1_2.webp', '1748579337_1748547738_diahinh1-2.jpg'),
(70, 24, 'ô tô', 'oto', '', '', 5000, 6000, 7999, 0, '2025-05-30 04:31:37', '1748579497_1748547632_diahinh1-2.jpg', '1748579497_1748539883_xe_java1_1.jfif', '1748579497_1748547738_diahinh2.jpg', '1748579497_1748547738_diahinh2.jpg'),
(71, 23, 'máy bay', 'maybay', '', '', 123, 123, 123, 0, '2025-05-30 18:33:32', '1748630012_1748547738_diahinh1-2.jpg', '1748630012_1748547632_diahinh1-1.jpg', '1748630012_1748539883_xe_java_1_2.webp', '1748630012_1748547738_diahinh1-1.jpg'),
(72, 23, 'xe tăng', 'xetaang', '', 'ngoc the dep trai dcd', 200, 300, 398, 0, '2025-05-30 18:48:08', '1748630888_1748547632_diahinh2.jpg', '1748630888_1748547738_diahinh1-2.jpg', '1748630888_1748547632_diahinh2.jpg', '1748630888_1748539883_xe_java.jfif'),
(73, 24, 'tau hoa', 'tauhoa', '', '', 500, 600, 693, 0, '2025-05-30 18:52:49', '1748631169_1748547632_diahinh2.jpg', '1748631169_1748547738_diahinh2.jpg', '1748631169_1748539883_xe_java_1_2.webp', '1748631169_1748547632_diahinh1-1.jpg'),
(74, 23, 'tau thuy', 'tau thuy', '', '', 500, 600, 699, 0, '2025-05-30 18:53:28', '1748631208_1748547738_diahinh1-1.jpg', '1748631208_1748547738_diahinh1-2.jpg', '1748631208_1748547632_diahinh2.jpg', '1748631208_1748539883_xe_java.jfif'),
(75, 24, 'xetaito', 'xetaico', 'xe conte to vcl', 'xe tai to vcl', 25252, 524535, 2450, 0, '2025-05-30 18:54:34', '1748631274_1748539883_xe_java_1_2.webp', '1748631274_1748539883_xe_java.jfif', '1748631274_1748547632_diahinh2.jpg', '1748631274_1748539883_xe_java_1_2.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(191) DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role_as` tinyint(4) NOT NULL DEFAULT 0,
  `creat_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `postal_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`, `role_as`, `creat_at`, `postal_code`) VALUES
(48, 'Hoàng Giang', 'hoanggiang@gmail.com', '0123456789', 'Hà Nội', '$2y$10$xdjSZRMIoP0YFO0YwU.iQ.skU42QD41hwyn6h4XXGlJB0rCmkgnvO', 0, '2023-05-05 13:02:43', NULL),
(49, 'Công Dinh', 'congdinh@gmail.com', '0123456789', NULL, '$2y$10$pOa6TRuABtdkJRk9HXcIzOvJNNu1UReWe1rIM/Yn27rOkX4RlqaLG', 1, '2023-05-05 13:33:13', NULL),
(50, 'Nguyễn Ngọc Thế', 'the@gmail.com', '12344556', NULL, '$2y$10$T0YU1y6qoufVyXFi6LuM0e6vVJvbTV0ZtuX/uu9IZbunx711FszOa', 0, '2025-05-17 01:43:37', NULL),
(52, 'admin', 'admin@gmail.com', '0987654321', 'Hà Nội', '$2y$10$PuIOcuMGxyjf/zOFMDezhOfnxDafagmPBu.kLps1GzxJN9RCfGqJi', 1, '2025-05-26 03:03:27', NULL),
(53, 'nguyễn hoàng anh', 'anh@gmail.com', '', NULL, '$2y$10$aDI87tCORZ49sINvkn0Ae.HlxjoSk3ncnHyAQRtzYzqGZ2kkYog9i', 0, '2025-05-26 17:31:57', NULL),
(54, 'nhanvien', 'nv@gmail.com', '', NULL, '$2y$10$eYzDjHOgfU9eTVg5JRkaze2csPvQYiCgHU5w/vpfC.AEOeIneIBti', 2, '2025-05-27 18:23:32', NULL),
(55, 'Mai Phương Loan', 'loan@gmail.com', '0123456777', 'nam định', '$2y$10$PWuk.URQZqo3VkaOdakRvOgH/Y0544FyaTsTfNRdz1sQ8PDvd8aWa', 0, '2025-05-28 09:03:03', '12345'),
(56, 'Đỗ Quang Vinh ', 'vinh@gmail.com', '0123456789', NULL, '$2y$10$dIaRFuN3gVr4DIT4BbtNmOg5mYP1v7359ddeqjwhQPHoCE5hRdJdq', 0, '2025-05-28 09:06:21', NULL),
(57, 'Phạm Đảm', 'dam@gmail.com', '0123456789', NULL, '$2y$10$nzuQN8op2mZc/RwVT9wPyusyK17d.3fUX1q/Va9mgrtbhPQD6ukTC', 0, '2025-05-28 09:10:10', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `order_detail_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
