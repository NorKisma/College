<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';

$teacher_id = $_SESSION['user_id'];
$class_id = (int)$_GET['class_id'];

$stmt = $conn->prepare("
    SELECT s.id, s.name
    FROM class_subjects cs
    JOIN subjects s ON s.id = cs.subject_id
    WHERE cs.class_id = ? AND cs.teacher_id = ?
");
$stmt->bind_param("ii", $class_id, $teacher_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()):
?>
<div class="row align-items-center mb-3">
    <div class="col-md-4 fw-bold"><?= htmlspecialchars($row['name']) ?></div>
    <div class="col-md-4">
        <input type="number" name="max_marks[<?= $row['id'] ?>]" class="form-control" placeholder="Max Marks" >
    </div>
    <div class="col-md-4">
        <input type="number" name="pass_marks[<?= $row['id'] ?>]" class="form-control" placeholder="Pass Marks" >
    </div>
</div>
<?php endwhile; ?>
