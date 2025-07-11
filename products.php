<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = 'SELECT name, `desc`, price, img FROM products';
    $result = $conn->query($sql);
    $products = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['name'], $data['desc'], $data['price'], $data['img'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }
    $stmt = $conn->prepare('INSERT INTO products (name, `desc`, price, img) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $data['name'], $data['desc'], $data['price'], $data['img']);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Insert failed: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
$conn->close(); 