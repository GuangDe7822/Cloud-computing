<?php
header('Content-Type: application/json');

$host = 'database-2.ce1mumaa6gf9.us-east-1.rds.amazonaws.com';
$db   = 'briteshop';
$user = 'admin';
$pass = 'admin12345678';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if table is empty
$result = $conn->query('SELECT COUNT(*) as count FROM products');
if ($result) {
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        // Insert dummy data
        $dummy = [
            ["Wireless Headphones", "Experience high-quality sound without the wires.", "$59.99", "https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=crop&w=400&q=80"],
            ["Smart Watch", "Track your fitness and stay connected on the go.", "$129.99", "https://images.unsplash.com/photo-1516574187841-cb9cc2ca948b?auto=format&fit=crop&w=400&q=80"],
            ["Eco Water Bottle", "Stay hydrated with this eco-friendly, reusable bottle.", "$19.99", "https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80"],
            ["Bluetooth Speaker", "Portable speaker with deep bass and long battery life.", "$39.99", "https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80"],
            ["Fitness Tracker", "Monitor your health and activity 24/7.", "$49.99", "https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80"],
            ["Classic Backpack", "Stylish and spacious for all your daily needs.", "$34.99", "https://images.unsplash.com/photo-1503602642458-232111445657?auto=format&fit=crop&w=400&q=80"]
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