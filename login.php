<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
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

    // تحقق من اسم المستخدم وكلمة المرور
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        exit();
    }

    $user = $result->fetch_assoc();

    // تحقق من كلمة المرور
    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }

    $stmt->close();
}
?>
