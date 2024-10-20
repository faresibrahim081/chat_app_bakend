<?php
session_start();
include('db.php');

// Set headers for CORS and content type
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log(print_r($_POST, true));

    // Check if POST data is received properly
    $sender = isset($_POST['sender']) ? $_POST['sender'] : null;
    $receiver = isset($_POST['receiver']) ? $_POST['receiver'] : null;
    $message = isset($_POST['message']) ? $_POST['message'] : null;
    
    if (empty($sender) || empty($receiver) || empty($message)) {
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO chat_messages (sender, receiver, message) VALUES (?, ?, ?)");
if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit();
}

// Bind parameters and execute the statement
$stmt->bind_param("sss", $sender, $receiver, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message sent']);
} else {
    echo json_encode(['error' => 'Failed to send message']);
}


    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
