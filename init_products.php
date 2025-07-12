<?php
header('Content-Type: application/json');

$host = 'database-2.ce1mumaa6gf9.us-east-1.rds.amazonaws.com';
$db   = 'briteshop';
$user = 'admin';
$pass = 'admin12345678';

// Connect to MySQL server (no DB yet)
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Create database if not exists
if (!$conn->select_db($db)) {
    if ($conn->query("CREATE DATABASE `$db`")) {
        $conn->select_db($db);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create database: ' . $conn->error]);
        exit();
    }
} else {
    $conn->select_db($db);
}

// Create products table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    `desc` TEXT NOT NULL,
    price VARCHAR(50) NOT NULL,
    img VARCHAR(500) NOT NULL
)";
if (!$conn->query($table_sql)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create table: ' . $conn->error]);
    exit();
}

// Check if table is empty
$result = $conn->query('SELECT COUNT(*) as count FROM products');
if ($result) {
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        // Insert dummy data
        $dummy = [
            ["Classic Denim Jacket", "Timeless blue denim jacket for all seasons.", "$69.99", "https://briteshop-product-images-sample.s3.us-east-1.amazonaws.com/Classic_Denim_Jacket.jpg"],
            ["Summer Floral Dress", "Lightweight floral dress perfect for summer outings.", "$49.99", "https://briteshop-product-images-sample.s3.us-east-1.amazonaws.com/Summer+Floral+Dress.jpg"],
            ["Men's White Sneakers", "Versatile white sneakers for everyday wear.", "$59.99", "https://briteshop-product-images-sample.s3.us-east-1.amazonaws.com/Mens_White_Sneakers.jpg"],
            ["Women's Leather Handbag", "Elegant brown leather handbag for any occasion.", "$89.99", "https://briteshop-product-images-sample.s3.us-east-1.amazonaws.com/Women's+Leather+Handbag.jpg"],
            ["Unisex Hoodie", "Comfy and stylish hoodie available in multiple colors.", "$39.99", "https://briteshop-product-images-sample.s3.us-east-1.amazonaws.com/Unisex+Hoodie.jpg"],
            ["Aviator Sunglasses", "Trendy aviator sunglasses with UV protection.", "$24.99", "https://briteshop-product-images-sample.s3.us-east-1.amazonaws.com/Aviator+Sunglasses.jpg"]
        ];
        $stmt = $conn->prepare('INSERT INTO products (name, `desc`, price, img) VALUES (?, ?, ?, ?)');
        foreach ($dummy as $d) {
            $stmt->bind_param('ssss', $d[0], $d[1], $d[2], $d[3]);
            $stmt->execute();
        }
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Dummy data inserted.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Table already has data.']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
}
$conn->close(); 