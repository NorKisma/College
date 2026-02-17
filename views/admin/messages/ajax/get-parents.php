<?php
require_once '../../../../config/db.php';

$data=json_decode(file_get_contents("php://input"),true);

$where=[];

if(!empty($data['classes'])){
  $in=implode(',',array_map('intval',$data['classes']));
  $where[]="cs.class_id IN ($in)";
}
if(!empty($data['sections'])){
  $in=implode(',',array_map('intval',$data['sections']));
  $where[]="cl.section_id IN ($in)";
}

$sql="
SELECT DISTINCT p.id, p.name
FROM parents p
JOIN students s ON s.guardian_phone=p.phone
JOIN class_students cs ON cs.student_id=s.id
JOIN classes cl ON cl.id=cs.class_id
".($where?" WHERE ".implode(" AND ",$where):"");

$res=$conn->query($sql);

$out=[];
while($r=$res->fetch_assoc()) $out[]=$r;

echo json_encode($out);
