<?php
header('Content-Type: application/json');
include('database.php');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$entries = $data['entries'];

$sql = "UPDATE users SET entries = '$entries' WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    $response = array('success' => true);
} else {
    $response = array('success' => false, 'error' => $conn->error);
}

echo json_encode($response);
