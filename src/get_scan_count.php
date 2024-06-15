<?php
session_start();
header('Content-Type: application/json');

require_once 'database.php';

$user_id = $_GET['user_id'] ?? $_SESSION['user_id'];

$query = "SELECT scan_count FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'scan_count' => $row['scan_count']]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
