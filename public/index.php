<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}
require_once '../src/database.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT name, scan_count FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Recognition Brain</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script defer src="js/script.js"></script>
</head>

<body>
    <div id="app">
        <nav>
            <p class="nl"><a href="index.php">Home</a></p>
            <p class="nl"><a href="profile.php">Profile</a></p>
            <p class="nl"><a href="rankings.php">Rankings</a></p>
            <p class="ns" onclick="logout()">Sign Out</p>
        </nav>
        <div class="content">
            <h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
            <p>Your current scan count is: <span id="scan-count"><?php echo htmlspecialchars($user['scan_count']); ?></span></p>
            <input type="hidden" id="user-id" value="<?php echo htmlspecialchars($user_id); ?>">
            <div class="if">
                <input type="text" id="iu" placeholder="Enter image URL">
                <button onclick="sbmt()">Detect</button>
            </div>
            <div class="id">
                <img id="ii" src="" alt="Image to analyze">
                <div id="li" class="loading" style="display: none;">
                    <div class="spin"></div>
                </div>
            </div>
            <div id="em" class="error" style="display: none;"></div>
        </div>
    </div>
</body>

</html>