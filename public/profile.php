<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.html');
    exit();
}

require_once '../src/database.php';

$user_id = $_GET['id'] ?? $_SESSION['user_id'];
$is_owner = $user_id == $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $commented_user_id = $user_id;

    $query = "INSERT INTO comments (user_id, commented_user_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iis', $_SESSION['user_id'], $commented_user_id, $comment);
    $stmt->execute();
}

$query = "SELECT comments.comment, users.name FROM comments JOIN users ON comments.user_id = users.id WHERE comments.commented_user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$comments = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
        <div class="pc">
            <h2><?php echo htmlspecialchars($user['name']); ?>'s Profile</h2>
            <?php if ($is_owner) : ?>
                <form action="../src/update_profile.php" method="POST" class="pf">
                    <div class="fg">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="fg">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit">Update Profile</button>
                </form>
            <?php else : ?>
                <form action="profile.php?id=<?php echo $user_id; ?>" method="POST">
                    <textarea name="comment" required></textarea>
                    <button type="submit">Add Comment</button>
                </form>
            <?php endif; ?>

            <h3>Comments</h3>
            <div class="comments">
                <?php
                while ($row = $comments->fetch_assoc()) {
                    echo "<p><strong>" . htmlspecialchars($row['name']) . ":</strong> " . htmlspecialchars($row['comment']) . "</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>