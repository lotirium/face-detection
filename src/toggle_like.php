<?php
session_start();
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $liked_user_id = $data['likedUserId'];
    $user_id = $_SESSION['user_id'];

    $query = "SELECT id FROM likes WHERE user_id = ? AND liked_user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $user_id, $liked_user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $query = "DELETE FROM likes WHERE user_id = ? AND liked_user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $liked_user_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'liked' => false]);
    } else {
        $query = "INSERT INTO likes (user_id, liked_user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $liked_user_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'liked' => true]);
    }
} else {
    echo json_encode(['success' => false]);
}
