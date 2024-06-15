<?php
session_start();
header('Content-Type: application/json');

require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$iurl = $data['iurl'] ?? '';

if (empty($iurl)) {
    echo json_encode(['success' => false, 'message' => 'Image URL is required']);
    exit();
}

$api_key = '93b8444a9ea34e19ac124fab840bb609';
$model_id = 'face-detection';
$model_version_id = '6dc7e46bc9124c5c8824be4822abe105';

$payload = json_encode([
    'inputs' => [
        [
            'data' => [
                'image' => [
                    'url' => $iurl
                ]
            ]
        ]
    ]
]);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.clarifai.com/v2/models/$model_id/versions/$model_version_id/outputs");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Key ' . $api_key,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'error' => curl_error($ch)]);
    curl_close($ch);
    exit();
}

curl_close($ch);

$response_data = json_decode($response, true);

if (isset($response_data['outputs'][0]['data']['regions'])) {
    $user_id = $_SESSION['user_id'];
    $query = "UPDATE users SET scan_count = scan_count + 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    echo json_encode(['success' => true, 'data' => $response_data['outputs'][0]['data']['regions']]);
} else {
    echo json_encode(['success' => false, 'error' => 'No faces detected']);
}
