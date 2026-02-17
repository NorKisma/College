<?php
require_once '../../../../config/db.php';

$data=json_decode(file_get_contents("php://input"),true);

$where=[];
$params=[];

if(!empty($data['subjects'])){
  $in=implode(',',array_map('intval',$data['subjects']));
  $where[]="cs.subject_id IN ($in)";
}
if(!empty($data['classes'])){
  $in=implode(',',array_map('intval',$data['classes']));
  $where[]="cs.class_id IN ($in)";
}
if(!empty($data['days'])){
  $in="'".implode("','",$data['days'])."'";
  $where[]="cs.day_of_week IN ($in)";
}

$sql="
SELECT DISTINCT t.id, CONCAT(t.first_name,' ',t.last_name) name
FROM teachers t
JOIN class_subjects cs ON cs.teacher_id=t.id
WHERE t.status='active'
".($where?" AND ".implode(" AND ",$where):"");

$res=$conn->query($sql);

$out=[];
while($r=$res->fetch_assoc()) $out[]=$r;

echo json_encode($out);
