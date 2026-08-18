<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'not_logged_in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'missing_product_id']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $data['product_id'];

$stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $delete_stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $delete_stmt->bind_param("ii", $user_id, $product_id);
    $delete_stmt->execute();
    echo json_encode(['status' => 'success', 'action' => 'removed']);
} else {
    $insert_stmt = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    $insert_stmt->bind_param("ii", $user_id, $product_id);
    $insert_stmt->execute();
    echo json_encode(['status' => 'success', 'action' => 'added']);
}
?>