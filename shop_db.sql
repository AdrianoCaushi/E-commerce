-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2025 at 02:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL,
  `quantity` int(100) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(17, 2, 25, 1),
(18, 2, 26, 1);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `message`) VALUES
(1, 1, 'website nuk funksionon sic duhet'),
(2, 1, 'Per sa kohe mund te vije porosia'),
(3, 2, 'Kur do te kete me shume produkte ne shitje?');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(1, 1, 'kash', 'adresa: Rruga Imer Ndregjoni Tirane Shqiperi - 1001', 'OnePlus 10 Pro ( 1 ), Apple iPhone 14 Pro ( 1 ), Hp Omen 40L Gaming Desktop ( 1 ), Apple iMac 24″ 4.5K ( 1 )', 7288, '20-Feb-2025', 'pending'),
(5, 2, 'kash', 'adresa: Belul Hatibi Tirane Shqiperi - 1001', 'Apple iPhone 14 Pro ( 1 ), OnePlus 10 Pro ( 1 ), Apple iPhone 14 Pro Max ( 1 )', 4488, '20-Feb-2025', 'E kompletuar'),
(6, 1, 'kash', 'adresa: Rruga e barrikadave Tirane Shqiperi - 1004', 'iPhone 16 Pro Max ( 10 )', 11500, '20-Feb-2025', 'pending');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
  DECLARE products_list TEXT DEFAULT NEW.total_products;
  DECLARE current_product TEXT;
  DECLARE comma_pos INT;
  DECLARE product_name VARCHAR(100);
  DECLARE product_qty INT;

  -- Loop through each product entry in total_products
  WHILE products_list != '' DO
    -- Find the position of the next comma separator
    SET comma_pos = LOCATE(', ', products_list);
    
    IF comma_pos > 0 THEN
      -- Extract current product and remove it from the list
      SET current_product = SUBSTRING(products_list, 1, comma_pos - 1);
      SET products_list = SUBSTRING(products_list, comma_pos + 2);
    ELSE
      -- Handle the last product in the list
      SET current_product = products_list;
      SET products_list = '';
    END IF;

    -- Extract product name and quantity from current_product
    SET product_name = TRIM(SUBSTRING_INDEX(current_product, ' ( ', 1));
    SET product_qty = CAST(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(current_product, ' )', 1), ' ( ', -1)) AS UNSIGNED);

    -- Update the product stock
    UPDATE products
    SET stock = stock - product_qty
    WHERE name = product_name;
  END WHILE;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(20) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `stock` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `details`, `price`, `image`, `stock`) VALUES
(24, 'OnePlus 10 Pro', 'smartphone', '6.7\" AMOLED (1440 x 3216), 50MP Camera, 12GB RAM, 5000mAh', 990, 'one plus 10 pro.jpg', 11),
(25, 'Apple iPhone 14 Pro', 'smartphone', '6.1\" XDR OLED, 48MP Camera, 128GB ROM, 3200mAh', 1499, 'apple iphone 14 pro.jpg', 9),
(26, 'Apple iPhone 14 Pro Max', 'smartphone', 'Apple iPhone 14 Pro Max', 1999, 'apple iphone 14 max.jpg', 13),
(27, 'Asus ROG Phone 6', 'smartphone', '6.78\" AMOLED, 50MP Camera, 16GB RAM, 6000mAh', 1000, 'asus rog phone 6.jpg', 12),
(28, 'Xiaomi 12 Lite', 'smartphone', '6.55\" AMOLED, 108MP Camera, 8GB RAM, 4300mAh', 479, 'xiaomi 12 lite.jpg', 10),
(29, 'Samsung Galaxy S22 Ultra', 'smartphone', '6.8\" AMOLED, 108MP Camera, 12GB RAM, 5000mAh', 989, 'samsung galaxy s22 ultra.jpg', 14),
(30, 'Samsung Galaxy S22', 'smartphone', '6.1\" AMOLED, 50MP Camera, 8GB RAM, 3700mAh', 760, 'samsumg galaxy s22.jpg', 12),
(31, 'Amazfit GTR 3 Pro', 'smartwatch', '1.45\" AMOLED, Touchscreen, Water Resistant, 450mAh', 240, 'amazfit gtr 3 pro.jpg', 10),
(32, 'Apple Watch Series 8', 'smartwatch', '1.9\" LTPO OLED, 32GB Memory, Water Resistant', 485, 'apple watch series 8.jpg', 14),
(33, 'Apple Watch Ultra', 'smartwatch', '1.92\" Retina OLED, 32GB Memory, Water Resistant', 940, 'apple watch ultra.jpg', 12),
(34, 'Samsung Galaxy Watch5', 'smartwatch', 'Super AMOLED, Route workout, Sleep Tracking', 310, 'samsung galaxy watch5.jpg', 10),
(35, 'Huawei Watch GT 3', 'smartwatch', '1.43\" AMOLED, 14 days battery, Waterproof', 400, 'huawei watch gt 3.jpg', 12),
(36, 'Huawei Watch 3 Pro', 'smartwatch', '1.43\" AMOLED, Wireless Charging, Water Resistant', 490, 'huawei watch 3 pro.jpg', 14),
(37, 'Airpods 2nd Generation', 'kufje', 'Bluetooth 5.0, Dual Optical Sensor, Up to 5hrs', 125, '2ng gen.jpg', 10),
(38, 'AirPods 3rd Generation', 'kufje', 'Bluetooth 5.0, Water Resistant, Adaptive EQ', 199, 'airpods 3rd gen.jpg', 12),
(39, 'AirPods Max', 'kufje', 'Noise Cancellation, Bluetooth 5.0, Up to 20hrs', 740, 'air pod max.jpg', 14),
(42, 'Apple iMac 24″ 4.5K', 'pcs', 'Apple M1, 8GB RAM, 256GB SSD, 24\" Retina 4.5K', 2100, 'apple imac.jpg', 14),
(43, 'Hp Omen 40L Gaming Desktop', 'pcs', 'AMD Ryzen 7, 32GB RAM, 1TB SSD, RTX 3070 Ti', 2699, 'hp omen.jpg', 10),
(44, 'Lenovo IdeaCentre AIO 5', 'pcs', 'Intel i5, 8GB RAM, 512GB SSD, 23.8\" FHD Display', 990, 'lenovo ideacentre.jpg', 12),
(45, 'Apple MacBook Pro 16\"', 'pcs', 'M1 Max, 32GB RAM, 1TB SSD, Retina Display', 4500, 'macbook.jpg', 14),
(46, 'iPhone 16 Pro Max', 'smartphone', '6.9 inch, LTPO Super Retina XDR OLED, 120Hz, HDR10, Dolby Vision, 1000 nits (typ), 2000 nits (HBM), 48 MP, f/1.8, 24mm (wide), 1/1.28&#34;, 1.22µm, dual pixel', 1150, 'iphone_16_pro_max_gold_1_121a95b27b.jpg', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'user',
  `image` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `image`, `number`) VALUES
(1, 'Adriano Caushi', 'Adriano.Caushi@fti.edu.al', '1663e2a2d6d99d63f5f9e470ee711da5', 'user', 'stock-photo-142984111.jpg', '+355683030995'),
(2, 'Erind', 'ca@gmail.com', '1663e2a2d6d99d63f5f9e470ee711da5', 'user', 'stock-photo-142984111.jpg', '+355683030996'),
(3, 'Adriano Admin', 'caushiadriano@gmail.com', '1663e2a2d6d99d63f5f9e470ee711da5', 'admin', 'stock-photo-142984111.jpg', '+355683030995');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`) VALUES
(5, 2, 27),
(6, 2, 29),
(7, 2, 31);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
