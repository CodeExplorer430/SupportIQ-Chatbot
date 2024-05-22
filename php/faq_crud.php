<?php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chatbot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $question = $data['question'];
    $answer = $data['answer'];
    
    $stmt = $conn->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)");
    $stmt->bind_param("ss", $question, $answer);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    
    $stmt->close();
}

$conn->close();
?>
