<?php
require_once '../../../../config/db.php';
require_once '../../../../config/sessions.php';

$data=json_decode(file_get_contents("php://input"),true);

if(empty($data['recipients']) || !$data['title'] || !$data['body']){
  echo json_encode(['message'=>'Invalid request']); exit;
}

$admin_id=$_SESSION['user_id'];
$count=0;

$stmt=$conn->prepare("
INSERT INTO messages
(sender_id,sender_type,recipient_type,recipient_id,message_title,message_body)
VALUES (?,?,?, ?,?,?)
");

foreach($data['recipients'] as $rid){
  $stmt->bind_param(
    "ississ",
    $admin_id,
    $st='admin',
    $data['recipient_type'],
    $rid,
    $data['title'],
    $data['body']
  );
  $stmt->execute();
  $count++;
}

echo json_encode(['message'=>"Message sent to $count recipients"]);
