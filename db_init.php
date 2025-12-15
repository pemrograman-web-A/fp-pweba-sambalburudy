<?php
/**
 * Database initialization script
 * This script creates the database tables and inserts initial data
 */

require_once 'koneksi.php';

echo "Starting database initialization...\n";

try {
    // Drop existing tables if they exist
    $dropTables = [
        'transaction_details',
        'transactions', 
        'order_details',
        'orders',
        'reviews',
        'products',
        'settings',
        'users'
    ];
    
    foreach ($dropTables as $table) {
        $sql = "DROP TABLE IF EXISTS `$table`";
        if ($conn->query($sql) === TRUE) {
            echo "Dropped table $table\n";
        } else {
            echo "Error dropping table $table: " . $conn->error . "\n";
        }
    }

    // Create users table
    $sql = "CREATE TABLE `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM('user', 'admin') DEFAULT 'user',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table users\n";
    } else {
        echo "Error creating users table: " . $conn->error . "\n";
    }

    // Create products table
    $sql = "CREATE TABLE `products` (
        `product_id` VARCHAR(10) NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `price` DECIMAL(10,2) NOT NULL,
        `stock` INT NOT NULL,
        `image` VARCHAR(255),
        `category` VARCHAR(50) DEFAULT 'Lainnya',
        PRIMARY KEY (`product_id`)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table products\n";
    } else {
        echo "Error creating products table: " . $conn->error . "\n";
    }

    // Create orders table
    $sql = "CREATE TABLE `orders` (
        `order_id` INT AUTO_INCREMENT PRIMARY KEY,
        `customer_name` VARCHAR(100) NOT NULL,
        `customer_phone` VARCHAR(20),
        `customer_email` VARCHAR(100),
        `shipping_address` TEXT,
        `total_amount` DECIMAL(15,2) NOT NULL,
        `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `status` VARCHAR(50) DEFAULT 'Diproses'
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table orders\n";
    } else {
        echo "Error creating orders table: " . $conn->error . "\n";
    }

    // Create order_details table
    $sql = "CREATE TABLE `order_details` (
        `detail_id` INT AUTO_INCREMENT PRIMARY KEY,
        `order_id` INT NOT NULL,
        `product_id` VARCHAR(10) NOT NULL,
        `quantity` INT NOT NULL,
        `price_at_order` DECIMAL(10,2) NOT NULL
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table order_details\n";
    } else {
        echo "Error creating order_details table: " . $conn->error . "\n";
    }

    // Create transactions table
    $sql = "CREATE TABLE `transactions` (
        `transaction_id` INT AUTO_INCREMENT PRIMARY KEY,
        `transaction_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `total_amount` DECIMAL(15,2) NOT NULL,
        `total_items` INT NOT NULL,
        `status` VARCHAR(50) DEFAULT 'COMPLETED'
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table transactions\n";
    } else {
        echo "Error creating transactions table: " . $conn->error . "\n";
    }

    // Create transaction_details table
    $sql = "CREATE TABLE `transaction_details` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `transaction_id` INT NOT NULL,
        `product_id` VARCHAR(10) NOT NULL,
        `quantity` INT NOT NULL,
        `subtotal` DECIMAL(15,2) NOT NULL
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table transaction_details\n";
    } else {
        echo "Error creating transaction_details table: " . $conn->error . "\n";
    }

    // Create reviews table
    $sql = "CREATE TABLE `reviews` (
        `review_id` INT AUTO_INCREMENT PRIMARY KEY,
        `product_id` VARCHAR(10) NOT NULL,
        `customer_name` VARCHAR(100),
        `rating` INT NOT NULL,
        `comment` TEXT,
        `review_date` DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table reviews\n";
    } else {
        echo "Error creating reviews table: " . $conn->error . "\n";
    }

    // Create settings table
    $sql = "CREATE TABLE `settings` (
        `setting_key` VARCHAR(50) PRIMARY KEY,
        `setting_value` TEXT
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table settings\n";
    } else {
        echo "Error creating settings table: " . $conn->error . "\n";
    }

    // Add foreign key constraints
    $foreignKeys = [
        "ALTER TABLE `order_details` ADD CONSTRAINT `fk_order_details_order` 
          FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE",
          
        "ALTER TABLE `order_details` ADD CONSTRAINT `fk_order_details_product` 
          FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)",
          
        "ALTER TABLE `transaction_details` ADD CONSTRAINT `fk_transaction_details_transaction` 
          FOREIGN KEY (`transaction_id`) REFERENCES `transactions`(`transaction_id`) ON DELETE CASCADE",
          
        "ALTER TABLE `transaction_details` ADD CONSTRAINT `fk_transaction_details_product` 
          FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)",
          
        "ALTER TABLE `reviews` ADD CONSTRAINT `fk_reviews_product` 
          FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE"
    ];

    foreach ($foreignKeys as $fkSql) {
        if ($conn->query($fkSql) === TRUE) {
            echo "Added foreign key constraint\n";
        } else {
            echo "Error adding foreign key constraint: " . $conn->error . "\n";
        }
    }

    // Insert sample data
    $insertData = [
        INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `image`, `category`) VALUES
        ('BR001', 'Sambal Bawang (Tutup Kuning)', 'Pedas nendang, tutup kuning legendaris.', 28000, 450, 'Sambal Bawang(tutup kuning).webp', 'Sambal'),
        ('BR002', 'Udang Crispy (Toples Tabung)', 'Udang renyah kemasan tabung.', 100000, 25, 'Udang Crispy(toples tabung).webp', 'Lauk'),
        ('BR003', 'Spikoe Tanpa Kismis', 'Lapis Surabaya original lembut.', 125000, 320, 'spikoe tanpa kismis.webp', 'Kue'),
        ('BR004', 'Sambal Bawang Sachet (Box)', 'Kemasan travel friendly isi praktis.', 37000, 100, 'Sambal Bawang Sachet Bu Rudy (1).webp', 'Sambal'),
        ('BR005', 'Spikoe Kismis', 'Lapis Surabaya klasik dengan kismis.', 125000, 50, 'spikoe kismis.webp', 'Kue'),
        ('BR006', 'Otaji Oseng Tuna Asap', 'Tuna asap pedas (500gr).', 85000, 75, 'Oseng Tuna Asap Otaji (1).webp', 'Lauk'),
        ('BR007', 'Almond Crispy Wisata Rasa', 'Camilan tipis super renyah.', 65000, 60, 'Almond Crispy Wisata Rasa.png', 'Cemilan'),
        ('BR008', 'Keripik Singkong Lumba-Lumba', 'Keripik singkong manis empuk.', 35000, 100, 'Keripik Singkong Manis Lumba Lumba.png', 'Cemilan'),
        ('BR009', 'Spikoe Resep Kuno', 'Varian resep kuno spesial.', 125000, 50, 'spikoe resep kuno.jpg', 'Kue');
                
        "INSERT INTO `settings` VALUES
        ('nomor_telepon_cs', '08123456789'),
        ('slogan_utama', 'Pedasnya Bikin Nagih!')",
        
        "INSERT INTO `reviews` (`product_id`, `customer_name`, `rating`, `comment`, `review_date`) VALUES
        ('BR001', 'Ani S.', 5, 'Mantap pol pedesnya!', NOW()),
        ('BR004', 'Budi Santoso', 5, 'Udangnya krispi banget.', NOW())",
        
        "INSERT INTO `transactions` (`transaction_id`, `transaction_date`, `total_amount`, `total_items`, `status`) VALUES
        (1, NOW(), 125000, 5, 'COMPLETED'),
        (2, DATE_SUB(NOW(), INTERVAL 2 HOUR), 56000, 2, 'COMPLETED')",
        
        "INSERT INTO `transaction_details` (`transaction_id`, `product_id`, `quantity`, `subtotal`) VALUES
        (1, 'BR001', 3, 75000), (1, 'BR002', 2, 50000),
        (2, 'BR002', 2, 56000)",
        
        "INSERT INTO `transactions` (`transaction_id`, `transaction_date`, `total_amount`, `total_items`, `status`) VALUES
        (3, DATE_SUB(NOW(), INTERVAL 1 DAY), 200000, 6, 'COMPLETED')",
        
        "INSERT INTO `transaction_details` (`transaction_id`, `product_id`, `quantity`, `subtotal`) VALUES
        (3, 'BR004', 4, 140000), (3, 'BR006', 2, 60000)",
        
        "INSERT INTO `orders` (`order_id`, `customer_name`, `customer_phone`, `customer_email`, `shipping_address`, `total_amount`, `status`, `order_date`) VALUES
        (1, 'Budi Santoso', '081234567890', 'budi@example.com', 'Jl. Dharmawangsa No. 10, Surabaya', 85000, 'Menunggu Pembayaran', NOW()),
        (2, 'Siti Aminah', '085678901234', 'siti@example.com', 'Perumahan Graha Famili Blok B-12, Surabaya', 150000, 'Perlu Konfirmasi Stok', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
        (3, 'Joko Widodo', '08111222333', 'joko@example.com', 'Apartemen Puncak Kertajaya Lt. 15, Surabaya', 200000, 'Menunggu Pembayaran', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
        (4, 'Dewi Persik', '081333444555', 'dewi@example.com', 'Jl. Tunjungan No. 55, Surabaya', 60000, 'Perlu Konfirmasi Stok', DATE_SUB(NOW(), INTERVAL 5 HOUR))",
        
        "INSERT INTO `order_details` (`order_id`, `product_id`, `quantity`, `price_at_order`) VALUES
        (1, 'BR001', 2, 28000), (1, 'BR002', 1, 29000),
        (2, 'BR004', 5, 30000),
        (3, 'BR001', 5, 28000), (3, 'BR003', 2, 30000),
        (4, 'BR003', 2, 30000)"
    ];

    foreach ($insertData as $insertSql) {
        if ($conn->query($insertSql) === TRUE) {
            echo "Inserted sample data\n";
        } else {
            echo "Error inserting data: " . $conn->error . "\n";
        }
    }

    echo "Database initialization completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error during database initialization: " . $e->getMessage() . "\n";
}

$conn->close();
?>