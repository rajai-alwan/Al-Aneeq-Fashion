-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 18 يونيو 2026 الساعة 14:25
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `bank_transactions`
--

CREATE TABLE `bank_transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `payment_status` enum('pending','completed','failed','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `cart_items`
--

INSERT INTO `cart_items` (`cart_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(10, 1, 8, 1, '2026-06-18 10:46:12'),
(11, 1, 10, 1, '2026-06-18 10:56:56');

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'ملابس رجالية', 'أحدث صيحات الموضة الرجالية من بدلات، قمصان، وملابس كاجوال'),
(2, 'ملابس نسائية', 'فساتين سهرة، ملابس يومية، وأزياء نسائية راقية'),
(3, 'ملابس أطفال', 'ملابس مريحة وأنيقة لجميع الأعمار الناشئة'),
(4, 'إكسسوارات وأحذية', 'حقائب، أحذية، وأحزمة تكمل أناقتك');

-- --------------------------------------------------------

--
-- بنية الجدول `contacts`
--

CREATE TABLE `contacts` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `contacts`
--

INSERT INTO `contacts` (`message_id`, `name`, `email`, `subject`, `message`, `is_read`, `submitted_at`) VALUES
(1, 'Yousra mohra', 'Yousra@gmail.com', 'شكوى أو اقتراح', 'مشروع روعه', 1, '2026-06-14 12:02:58');

-- --------------------------------------------------------

--
-- بنية الجدول `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT 'cod',
  `payment_receipt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`, `shipping_address`, `payment_method`, `payment_receipt`) VALUES
(1, 2, '2026-06-14 12:08:53', 115.00, 'delivered', 'غزة', 'cod', NULL),
(2, 3, '2026-06-18 10:04:46', 60.00, 'paid', 'غزة', 'cod', NULL),
(3, 3, '2026-06-18 10:35:28', 120.00, 'pending', 'غزة', 'cod', NULL),
(4, 1, '2026-06-18 10:42:56', 70.00, 'pending', 'غزة', 'cod', NULL),
(5, 3, '2026-06-18 11:26:11', 60.00, 'pending', 'https://www.google.com/maps?q=31.519430,34.483337', 'cod', NULL),
(6, 3, '2026-06-18 11:30:14', 365.00, 'pending', 'غزة', 'cod', NULL),
(7, 3, '2026-06-18 12:07:53', 40.00, 'pending', 'https://www.google.com/maps?q=31.526044390851737,34.48217141995056', 'usdt', 'receipt_6a33df994bba6.jpeg'),
(8, 3, '2026-06-18 12:08:22', 60.00, 'pending', 'https://www.google.com/maps?q=31.526044390851737,34.48217141995056', 'usdt', 'receipt_6a33dfb64c943.jpeg'),
(9, 3, '2026-06-18 12:14:23', 60.00, 'pending', 'https://www.google.com/maps?q=31.526044390851737,34.48217141995056', 'usdt', 'receipt_6a33e11f1cd41.jpeg'),
(10, 3, '2026-06-18 12:14:51', 60.00, 'pending', 'https://www.google.com/maps?q=31.526044390851737,34.48217141995056', 'cod', NULL),
(11, 3, '2026-06-18 12:16:38', 75.00, 'pending', 'https://www.google.com/maps?q=31.526044390851737,34.48217141995056', 'cod', NULL),
(12, 3, '2026-06-18 12:17:05', 25.00, 'pending', 'https://www.google.com/maps?q=31.526044390851737,34.48217141995056', 'usdt', 'receipt_6a33e1c17ee7b.jpeg');

-- --------------------------------------------------------

--
-- بنية الجدول `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 7, 3, 25.00),
(2, 1, 10, 2, 20.00),
(3, 2, 8, 1, 60.00),
(4, 3, 8, 2, 60.00),
(5, 4, 11, 1, 70.00),
(6, 5, 8, 1, 60.00),
(7, 6, 10, 6, 20.00),
(8, 6, 7, 5, 25.00),
(9, 6, 8, 2, 60.00),
(10, 7, 10, 2, 20.00),
(11, 8, 8, 1, 60.00),
(12, 9, 8, 1, 60.00),
(13, 10, 8, 1, 60.00),
(14, 11, 7, 3, 25.00),
(15, 12, 7, 1, 25.00);

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock_quantity`, `image_url`, `category_id`, `created_at`) VALUES
(7, 'طقم الأناقة الكلاسيكية الرجالي', 'اجمع بين الراحة والأناقة مع طقم الأناقة الكلاسيكية الرجالي، المصمم بتناسق جذاب بين اللونين الكريمي والأزرق ليمنحك إطلالة عصرية وراقية في مختلف المناسبات. يتكون الطقم من قميص أنيق بأكمام طويلة وبنطال مريح بقصة عصرية تناسب الاستخدام اليومي والخروجات الخاصة.', 25.00, 38, 'images/product_6a2e93d431bbf3.04008018.jpeg', 1, '2026-06-14 11:43:16'),
(8, 'فستان نسمة الربيعي الأنيق', 'تألقي بإطلالة راقية ومريحة مع فستان نسمة الربيعي الأنيق، المصمم بنقوش خضراء ناعمة تضفي لمسة من الحيوية والجمال. يتميز بقصّة واسعة وانسيابية تمنحك الراحة طوال اليوم، مع حزام أنيق يبرز جمال القوام ويمنح مظهراً أنثوياً جذاباً.', 60.00, 31, 'images/product_6a2e93feafeac4.47156945.jpeg', 2, '2026-06-14 11:43:58'),
(10, 'معطف الأرنب الشتوي الفاخر للأطفال', 'امنحي طفلتكِ إطلالة ساحرة ودفئًا مثاليًا مع معطف الأرنب الشتوي الفاخر المصمم بخامة ناعمة للغاية ولمسات أنيقة تضفي مظهرًا لطيفًا ومميزًا. يتميز بقبعة مزينة بأذني أرنب لطيفتين وربطة أنيقة تضفي لمسة من الجمال والبراءة، ليكون الخيار المثالي للأيام الباردة والتصوير والمناسبات.', 20.00, 90, 'images/product_6a2e947b3ec120.04105940.jpeg', 2, '2026-06-14 11:46:03'),
(11, 'هودي سحاب كلاسيكي', 'استمتع بإطلالة عصرية تجمع بين الأناقة والبساطة مع هودي السحاب الكلاسيكي المصمم ليمنحك الراحة والدفء في جميع الأوقات. يتميز بقماش ناعم عالي الجودة، وقبعة عملية مع جيوب أمامية واسعة، ليكون خيارك المثالي للجامعة، العمل، الرياضة أو الخروجات اليومية.', 70.00, 49, 'images/product_6a2e94d1915333.72789407.jpeg', 1, '2026-06-14 11:47:29');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'Yousra mohra', 'Yousra@gmail.com', '$2y$10$vDHgw8DyktTIOWPOC2.WUO4eICAAzbcsLbqj.a7/JN//NF/r3N3DS', '⁦059-761-4536⁩', 'غزة', 'admin', '2026-06-14 11:35:22'),
(2, 'Yousra mohra', 'Y@gmail.com', '$2y$10$F9oFbUPZLIX62CW9.XNTqOr1vZM6PamLPeWqHONuJ0bIVBRJBiOwG', '⁦059-761-4536⁩', 'غزة', 'customer', '2026-06-14 12:02:12'),
(3, 'R', 'rajai.kh12345@gmail.com', '$2y$10$x4LvMII68HxoAeMoZ.cYYOIK4p3OgdQhajO/uEaoD92J60kZsynH6', '0597388092', 'غزة', 'customer', '2026-06-18 10:04:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_transactions`
--
ALTER TABLE `bank_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_transactions`
--
ALTER TABLE `bank_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- قيود الجداول `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- قيود الجداول `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- قيود الجداول `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
