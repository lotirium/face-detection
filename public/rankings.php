<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.html');
    exit();
}

require_once '../src/database.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT users.id, users.name, users.scan_count, 
                 EXISTS(SELECT 1 FROM likes WHERE likes.user_id = ? AND likes.liked_user_id = users.id) AS liked
          FROM users 
          ORDER BY users.scan_count DESC";
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
    <title>Rankings</title>
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
        <div class="rc">
            <h2>User Rankings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Scan Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $is_me = $row['id'] == $_SESSION['user_id'];
                        echo "<tr><td><a href='profile.php?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . ($is_me ? " (Me)" : "") . "</a></td><td>" . htmlspecialchars($row['scan_count']) . "</td>";
                        if (!$is_me) {
                            $like_class = $row['liked'] ? 'fas' : 'far';
                            echo "<td>
                                    <i id='like-" . htmlspecialchars($row['id']) . "' class='fa fa-heart " . $like_class . "' onclick='tlike(" . htmlspecialchars($row['id']) . ")'></i>
                                  </td></tr>";
                        } else {
                            echo "<td></td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>