<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.html');
    exit();
}

require_once 'database.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM activity_log WHERE user_id = ? ORDER BY timestamp DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="activity-log">
        <h2>Activity Log</h2>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <li><?php echo htmlspecialchars($row['action']); ?> at <?php echo htmlspecialchars($row['timestamp']); ?></li>
            <?php endwhile; ?>
        </ul>
        <button onclick="logout()">Sign Out</button>
    </div>
</body>
<script>
    function logout() {
        window.location.href = 'logout.php';
    }
</script>

</html>