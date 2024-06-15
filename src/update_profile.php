<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/signin.html');
    exit();
}

require_once 'database.php';

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];

$query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssi', $name, $email, $user_id);
if ($stmt->execute()) {
    $_SESSION['user_name'] = $name;
    header('Location: ../public/profile.php');
} else {
    echo "Error updating profile.";
}
