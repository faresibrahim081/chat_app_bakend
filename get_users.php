<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include('db.php');

// جلب جميع المستخدمين باستثناء المستخدم الحالي
$sql = "SELECT id, username FROM users";
// $sql = "SELECT username FROM users WHERE username != '$username'";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
