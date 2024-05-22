<?php

require("../php/chatbot.php");

$data = json_decode(file_get_contents("php://input"), true);
$message = $data['message'] ?? '';

$ChatBot = new ChatBot();
$resMessage = $ChatBot->sendMessage($message);

echo json_encode(['response' => $resMessage]);
exit;
?>
