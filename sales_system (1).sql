-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 02:23 PM
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
-- Database: `sales_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `total_sold` int(11) DEFAULT 0,
  `total_earned_on_item` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `item_code`, `name`, `description`, `stock`, `price`, `total_sold`, `total_earned_on_item`) VALUES
(1, '123', 'Cornetto Disc Matcha Crumble', '', 16, 10.00, 91, 910.00),
(2, '1234', 'Cornetto Cookies & Dream', '', 179, 123.00, 51, 408.00),
(3, '555', 'Cornetto Vanilla', '', 101, 32.00, 1, 32.00),
(4, '6666', 'Cornetto Chocolate', '', 101, 17.00, 7, 99.00),
(5, '11234', 'Cornetto Hazelnut', '', 21, 10.00, 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `receipt_no` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_tendered` decimal(10,2) NOT NULL,
  `change_due` decimal(10,2) NOT NULL,
  `mode_of_payment` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `staff_name` varchar(50) DEFAULT 'Admin Demo',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `receipt_no`, `total_amount`, `amount_tendered`, `change_due`, `mode_of_payment`, `customer_name`, `customer_phone`, `customer_email`, `staff_name`, `date_created`) VALUES
(1, 'REC-1772199079', 88.00, 100.00, 12.00, 'Cash', 'drwe', '09274213543', 'a@gmail.com', 'Admin Demo', '2026-02-27 13:31:19'),
(2, 'REC-1772199142', 88.00, 100.00, 12.00, 'Cash', 'drwe', '09274213543', 'a@gmail.com', 'Admin Demo', '2026-02-27 13:32:22'),
(3, 'REC-1772200504', 50.00, 50.00, 0.00, 'Cash', 'ff', '09274213547', 'an`@gmail.com', 'Admin Demo', '2026-02-27 13:55:04'),
(4, 'REC-956501', 48.00, 70.00, 22.00, 'Cash', 'daa', '09274231456', 'oy@gmail.com', 'Admin Demo', '2026-02-27 13:59:53'),
(5, 'REC-207A54', 16.00, 16.00, 0.00, 'Cash', 'An', '09274213540', 'j@gmail.com', 'Admin Demo', '2026-02-27 14:03:46'),
(6, 'REC-C0C412', 20.00, 100.00, 80.00, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 14:05:00'),
(7, 'REC-53D486', 40.00, 40.00, 0.00, 'Cash', 'Andrew Ramoy', '09274231549', 'a4@gmail.com', 'Admin Demo', '2026-02-27 14:06:13'),
(8, 'REC-5B57A6', 60.00, 1000.00, 940.00, 'Cash', 'Andrew Ramoy', '09274231544', 'a4@gmail.com', 'Admin Demo', '2026-02-27 14:07:33'),
(9, 'REC-FCD60C', 47.04, 50.00, 2.96, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 14:16:47'),
(10, 'REC-08B744', 39.00, 50.00, 11.00, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 16:35:12'),
(11, 'REC-1EFA80', 80.00, 80.00, 0.00, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 16:57:05'),
(12, 'REC-B1ED90', 100.00, 90.00, -10.00, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 17:13:31'),
(13, 'REC-F2FDF3', 20.40, 30.00, 9.60, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 17:15:27'),
(14, 'REC-8EA716', 60.00, 60.00, 0.00, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 17:20:56'),
(15, 'REC-4DD35B', 0.00, -3.00, 0.00, NULL, 'Walk-in Customer', NULL, NULL, 'Admin Demo', '2026-02-27 17:53:24'),
(16, 'REC-970977', 48.00, 60.00, 12.00, 'POS', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 18:00:41'),
(17, 'REC-176648', 62.00, 100.00, 38.00, 'Transfer', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-27 18:01:05'),
(18, 'REC-B31D9B', 30.00, 40.00, 10.00, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-28 12:11:23'),
(19, 'REC-A174D6', 20.00, 50.00, 30.00, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-28 12:15:06'),
(20, 'REC-F94038', 37.60, 80.00, 42.40, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-28 12:35:43'),
(21, 'REC-57D3D7', 483.60, 1000.00, 516.40, 'Cash', 'Walk-in Customer', '', '', 'Admin Demo', '2026-02-28 12:39:17');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_at_sale` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`, `price_at_sale`) VALUES
(1, 12, 1, 10, 10.00),
(2, 13, 1, 2, 10.00),
(3, 14, 4, 4, 15.00),
(4, 16, 2, 6, 8.00),
(5, 17, 3, 1, 32.00),
(6, 17, 1, 3, 10.00),
(7, 18, 1, 3, 10.00),
(8, 19, 1, 2, 10.00),
(9, 20, 1, 4, 10.00),
(10, 21, 1, 49, 10.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`),
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
