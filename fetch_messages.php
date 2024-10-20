<?php

include 'db.php';

// Set the CORS headers to allow requests from your frontend (React app)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Handle only GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve 'sender' and 'receiver' from the query parameters, sanitize them
    $sender = isset($_GET['sender']) ? mysqli_real_escape_string($conn, $_GET['sender']) : null;
    $receiver = isset($_GET['receiver']) ? mysqli_real_escape_string($conn, $_GET['receiver']) : null;

    // Check if sender and receiver are set
    if (!$sender || !$receiver) {
        echo json_encode(['error' => 'Sender or receiver not set']);
        exit;
    }

    // Prepare the SQL query to fetch messages between sender and receiver
    $query = "SELECT * FROM chat_messages WHERE 
        (sender = ? AND receiver = ?) 
        OR 
        (sender = ? AND receiver = ?) 
        ORDER BY created_at ASC";
    
    // Prepare the statement and bind parameters to avoid SQL injection
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssss', $sender, $receiver, $receiver, $sender);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch the messages and return them as an array
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        // Return the messages as JSON
        echo json_encode($messages);
    } else {
        // Handle query preparation errors
        echo json_encode(['error' => 'Failed to prepare SQL query']);
    }
} else {
    // Return error if the request method is not GET
    echo json_encode(['error' => 'Invalid request method']);
}
?>
