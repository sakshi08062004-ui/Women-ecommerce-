-- SHE SHOP - Women's E-Commerce Platform
-- Database Schema and Sample Data for MySQL / phpMyAdmin

CREATE DATABASE IF NOT EXISTS `women_ecommerce` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `women_ecommerce`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Admin User', 'admin@gmail.com', '$2y$12$eh9RVKgFTlSWGZyGQhLamOf4IVNfoIOin5iuIMBq/t/oT6o1BZEYy', 'admin'),
(2, 'Customer User', 'customer@example.com', '$2y$12$miS4dsUY5cB4jpurf6Wbg.udaAYnNiJ/7f2PouZHjdQll3dl7gs.i', 'customer');

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `image`) VALUES
(1, 'Sarees', 'sarees', 'https://images.pexels.com/photos/7920188/pexels-photo-7920188.jpeg?auto=compress&cs=tinysrgb&w=600'),
(2, 'Kurtas & Kurtis', 'kurtas-kurtis', 'https://images.pexels.com/photos/36311379/pexels-photo-36311379.jpeg?auto=compress&cs=tinysrgb&w=600'),
(3, 'Lehengas', 'lehengas', 'https://images.pexels.com/photos/30497750/pexels-photo-30497750.jpeg?auto=compress&cs=tinysrgb&w=600'),
(4, 'Salwar Suits', 'salwar-suits', 'https://images.pexels.com/photos/5399874/pexels-photo-5399874.jpeg?auto=compress&cs=tinysrgb&w=600');

-- --------------------------------------------------------
-- Table structure for `products`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 10,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `stock`, `image`) VALUES
(1, 1, 'Kanjivaram Silk Saree', 'kanjivaram-silk-saree', 'Authentic pure silk Kanjivaram saree in royal blue with gold zari temple borders.', 4527.00, 10, 'https://images.pexels.com/photos/7920188/pexels-photo-7920188.jpeg?auto=compress&cs=tinysrgb&w=600'),
(2, 1, 'Banarasi Bridal Saree', 'banarasi-bridal-saree', 'Exquisite maroon Banarasi saree featuring intricate gold brocade weaving.', 6019.00, 8, 'https://images.pexels.com/photos/12850123/pexels-photo-12850123.jpeg?auto=compress&cs=tinysrgb&w=600'),
(3, 1, 'Yellow Haldi Chiffon Saree', 'yellow-haldi-chiffon-saree', 'Lightweight festive yellow chiffon saree with delicate thread embroidery.', 2983.00, 15, 'https://images.pexels.com/photos/6842754/pexels-photo-6842754.jpeg?auto=compress&cs=tinysrgb&w=600'),
(4, 1, 'Pastel Pink Organza Saree', 'pastel-pink-organza-saree', 'Modern pastel pink organza saree decorated with delicate silver floral motifs.', 5595.00, 12, 'https://images.pexels.com/photos/30703859/pexels-photo-30703859.jpeg?auto=compress&cs=tinysrgb&w=600'),
(5, 2, 'Handcrafted Blue Chikankari Kurta', 'blue-chikankari-kurta', 'Pure georgette Lucknowi Chikankari kurta in pastel blue.', 3864.00, 20, 'https://images.pexels.com/photos/35521738/pexels-photo-35521738.jpeg?auto=compress&cs=tinysrgb&w=600'),
(6, 2, 'Heavy Embroidered Maroon Anarkali', 'embroidered-maroon-anarkali', 'Floor-length festive Anarkali suit with hand-beaded neckline.', 4200.00, 7, 'https://images.pexels.com/photos/37523792/pexels-photo-37523792.jpeg?auto=compress&cs=tinysrgb&w=600'),
(7, 2, 'Yellow Printed Cotton Kurti', 'yellow-printed-cotton-kurti', 'Breathable daily cotton kurti with traditional Jaipur block print.', 999.00, 25, 'https://images.pexels.com/photos/34077588/pexels-photo-34077588.jpeg?auto=compress&cs=tinysrgb&w=600'),
(8, 2, 'Pink Straight Rayon Kurta', 'pink-straight-rayon-kurta', 'Comfortable soft rayon kurta featuring subtle foil print details.', 1250.00, 18, 'https://images.pexels.com/photos/36311379/pexels-photo-36311379.jpeg?auto=compress&cs=tinysrgb&w=600'),
(9, 3, 'Royal Blue Velvet Bridal Lehenga', 'royal-blue-velvet-lehenga', 'Heavy designer lehenga choli with zardosi and sequence craftsmanship.', 8999.00, 5, 'https://images.pexels.com/photos/28405815/pexels-photo-28405815.jpeg?auto=compress&cs=tinysrgb&w=600'),
(10, 3, 'Crimson Red Wedding Lehenga', 'crimson-red-wedding-lehenga', 'Traditional bridal red lehenga choli with rich gotta patti embroidery.', 9500.00, 4, 'https://images.pexels.com/photos/28457317/pexels-photo-28457317.jpeg?auto=compress&cs=tinysrgb&w=600'),
(11, 3, 'Yellow Floral Georgette Lehenga', 'yellow-floral-georgette-lehenga', 'Bright and flowy yellow lehenga perfect for Haldi and Mehendi ceremonies.', 4500.00, 10, 'https://images.pexels.com/photos/28941551/pexels-photo-28941551.jpeg?auto=compress&cs=tinysrgb&w=600'),
(12, 3, 'Baby Pink Pearl Embellished Lehenga', 'baby-pink-pearl-lehenga', 'Exquisite net lehenga with pearl and crystal detailing for sangeet.', 6800.00, 6, 'https://images.pexels.com/photos/13204678/pexels-photo-13204678.jpeg?auto=compress&cs=tinysrgb&w=600'),
(13, 4, 'Royal Blue Silk Salwar Suit', 'royal-blue-silk-salwar-suit', 'Straight cut raw silk suit with contrast tissue dupatta.', 3400.00, 14, 'https://images.pexels.com/photos/13860638/pexels-photo-13860638.jpeg?auto=compress&cs=tinysrgb&w=600'),
(14, 4, 'Festive Red Punjabi Sharara Suit', 'festive-red-punjabi-sharara', 'Short kurta paired with flared sharara and gotta-laced border.', 3999.00, 9, 'https://images.pexels.com/photos/13723555/pexels-photo-13723555.jpeg?auto=compress&cs=tinysrgb&w=600'),
(15, 4, 'Yellow Floral Patiala Suit', 'yellow-floral-patiala-suit', 'Classic Punjabi Patiala suit in pure cotton with colorful phulkari dupatta.', 2150.00, 16, 'https://images.pexels.com/photos/33824984/pexels-photo-33824984.jpeg?auto=compress&cs=tinysrgb&w=600'),
(16, 4, 'Dusty Rose Embroidered Palazzo Suit', 'dusty-rose-palazzo-suit', 'Elegant modern palazzo suit with thread work on soft modal silk.', 2800.00, 11, 'https://images.pexels.com/photos/19271174/pexels-photo-19271174.jpeg?auto=compress&cs=tinysrgb&w=600');

-- --------------------------------------------------------
-- Table structure for `orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) NOT NULL DEFAULT 'cod',
  `payment_status` varchar(50) NOT NULL DEFAULT 'unpaid',
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `payment_method`, `payment_status`, `shipping_address`) VALUES
(1, 2, 4527.00, 'completed', 'cod', 'paid', 'A-402, Lotus Greens, Sector 78, Noida, UP - 201301'),
(2, 2, 8999.00, 'processing', 'card', 'paid', 'Flat 12, Sunrise Residency, Bandra West, Mumbai - 400050');

-- --------------------------------------------------------
-- Table structure for `order_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 4527.00),
(2, 2, 9, 1, 8999.00);

-- --------------------------------------------------------
-- Table structure for `wishlists`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_product_unique` (`user_id`,`product_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`) VALUES
(1, 2, 2),
(2, 2, 10);

-- --------------------------------------------------------
-- Table structure for `feedback`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `feedback_user_id_foreign` (`user_id`),
  CONSTRAINT `feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `feedback` (`id`, `user_id`, `name`, `email`, `subject`, `message`) VALUES
(1, 2, 'Customer User', 'customer@example.com', 'Saree Fabric Feedback', 'The Kanjivaram silk saree received was absolutely stunning! The zari work is pure quality. Highly recommended!'),
(2, NULL, 'Ananya Sen', 'ananya@example.com', 'Bridal Lehenga Inquiry', 'Do you provide custom blouse tailoring with the Bridal Red Lehenga? Thanks!');

SET FOREIGN_KEY_CHECKS = 1;
