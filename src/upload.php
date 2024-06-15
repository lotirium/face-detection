<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/signin.html');
    exit();
}

if (isset($_FILES['image'])) {
    $file = $_FILES['image'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $allowed = array('jpg', 'jpeg', 'png');

    $fileExt = strtolower(end(explode('.', $fileName)));

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) {
                $fileNameNew = uniqid('', true) . "." . $fileExt;
                $fileDestination = '../uploads/' . $fileNameNew;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    require_once 'database.php';
                    $user_id = $_SESSION['user_id'];
                    $query = "UPDATE users SET scan_count = scan_count + 1 WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();

                    header("Location: ../public/detect.php?file=" . $fileNameNew);
                } else {
                    echo "There was an error uploading your file.";
                }
            } else {
                echo "Your file is too large.";
            }
        } else {
            echo "There was an error uploading your file.";
        }
    } else {
        echo "You cannot upload files of this type.";
    }
} else {
    echo "No file was uploaded.";
}
