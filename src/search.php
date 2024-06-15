<?php
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchTerm = trim($_GET["search"]);

    $query = "SELECT id, name, email FROM users WHERE name LIKE ? OR email LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
}
