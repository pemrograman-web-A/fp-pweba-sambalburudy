SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `transaction_details`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `order_details`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `products` (
  `product_id` VARCHAR(10) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL,
  `category` VARCHAR(50) DEFAULT 'Lainnya',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`)
);

CREATE TABLE `orders` (
  `order_id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20),
  `customer_email` VARCHAR(100),
  `shipping_address` TEXT,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(50) DEFAULT 'Diproses' 
);

CREATE TABLE `order_details` (
  `detail_id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` VARCHAR(10) NOT NULL,
  `quantity` INT NOT NULL,
  `price_at_order` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)
);

CREATE TABLE `transactions` (
  `transaction_id` INT AUTO_INCREMENT PRIMARY KEY,
  `transaction_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `total_items` INT NOT NULL,
  `status` VARCHAR(50) DEFAULT 'COMPLETED'
);

CREATE TABLE `transaction_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` INT NOT NULL,
  `product_id` VARCHAR(10) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (`transaction_id`) REFERENCES `transactions`(`transaction_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)
);

CREATE TABLE `reviews` (
  `review_id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` VARCHAR(10) NOT NULL,
  `customer_name` VARCHAR(100),
  `rating` INT NOT NULL,
  `comment` TEXT,
  `review_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE
);

CREATE TABLE `settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` TEXT
);

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `category`) VALUES
('BR001', 'Sambal Bawang Bu Rudy (Tutup Kuning)', 'Sambal bawang legendaris khas Surabaya.', 25000, 150, 'Sambal'),
('BR002', 'Sambal Terasi Bajak (Tutup Merah)', 'Sambal terasi dengan rasa manis gurih.', 28000, 100, 'Sambal'),
('BR003', 'Sambal Ijo (Tutup Hijau)', 'Sambal cabai hijau dengan ikan peda.', 28000, 80, 'Sambal'),
('BR004', 'Udang Crispy Kecil', 'Udang goreng tepung renyah.', 35000, 200, 'Lauk'),
('BR005', 'Udang Crispy Besar', 'Udang goreng tepung ukuran besar.', 65000, 50, 'Lauk'),
('BR006', 'Almond Crispy Cheese', 'Cemilan tipis renyah taburan almond.', 55000, 45, 'Cemilan');

INSERT INTO `settings` VALUES 
('nomor_telepon_cs', '08123456789'),
('slogan_utama', 'Pedasnya Bikin Nagih!');

INSERT INTO `reviews` (`product_id`, `customer_name`, `rating`, `comment`, `review_date`) VALUES
('BR001', 'Ani S.', 5, 'Mantap pol pedesnya!', NOW()),
('BR004', 'Budi Santoso', 5, 'Udangnya krispi banget.', NOW());

INSERT INTO `transactions` (`transaction_id`, `transaction_date`, `total_amount`, `total_items`, `status`) VALUES
(1, NOW(), 125000, 5, 'COMPLETED'), 
(2, DATE_SUB(NOW(), INTERVAL 2 HOUR), 56000, 2, 'COMPLETED');

INSERT INTO `transaction_details` (`transaction_id`, `product_id`, `quantity`, `subtotal`) VALUES
(1, 'BR001', 3, 75000), (1, 'BR002', 2, 50000),
(2, 'BR002', 2, 56000);

INSERT INTO `transactions` (`transaction_id`, `transaction_date`, `total_amount`, `total_items`, `status`) VALUES
(3, DATE_SUB(NOW(), INTERVAL 1 DAY), 200000, 6, 'COMPLETED');

INSERT INTO `transaction_details` (`transaction_id`, `product_id`, `quantity`, `subtotal`) VALUES
(3, 'BR004', 4, 140000), (3, 'BR006', 2, 60000);

INSERT INTO `orders` (`order_id`, `customer_name`, `customer_phone`, `customer_email`, `shipping_address`, `total_amount`, `status`, `order_date`) VALUES
(1, 'Budi Santoso', '081234567890', 'budi@example.com', 'Jl. Dharmawangsa No. 10, Surabaya', 85000, 'Menunggu Pembayaran', NOW()),
(2, 'Siti Aminah', '085678901234', 'siti@example.com', 'Perumahan Graha Famili Blok B-12, Surabaya', 150000, 'Perlu Konfirmasi Stok', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, 'Joko Widodo', '08111222333', 'joko@example.com', 'Apartemen Puncak Kertajaya Lt. 15, Surabaya', 200000, 'Menunggu Pembayaran', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(4, 'Dewi Persik', '081333444555', 'dewi@example.com', 'Jl. Tunjungan No. 55, Surabaya', 60000, 'Perlu Konfirmasi Stok', DATE_SUB(NOW(), INTERVAL 5 HOUR));

INSERT INTO `order_details` (`order_id`, `product_id`, `quantity`, `price_at_order`) VALUES
(1, 'BR001', 2, 28000), (1, 'BR002', 1, 29000),
(2, 'BR004', 5, 30000),
(3, 'BR001', 5, 28000), (3, 'BR003', 2, 30000),
(4, 'BR003', 2, 30000);

SET FOREIGN_KEY_CHECKS = 1;