<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // الحصول على البيانات من JSON
    $data = json_decode(file_get_contents("php://input"), true);
    
    $username = $data['username'] ?? null; // استخدام ?? للتأكد من عدم وجود أخطاء
    $password = $data['password'] ?? null;

    if (!$username || !$password) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        exit();
    }

    // تحقق إذا كان اسم المستخدم موجود بالفعل
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit();
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword); // تأكد من استخدام hashed password
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    }
    $stmt->close();
}
?>
