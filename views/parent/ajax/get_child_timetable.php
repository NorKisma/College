<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$child_id = $_POST['child_id'];

// Get student's class
$class_query = $conn->prepare("
    SELECT cs.class_id 
    FROM class_students cs 
    WHERE cs.student_id = ? AND cs.is_active = 1
");
$class_query->bind_param("i", $child_id);
$class_query->execute();
$class_result = $class_query->get_result();
$class = $class_result->fetch_assoc();

if ($class) {
    $class_id = $class['class_id'];
    
    $query = $conn->prepare("
        SELECT 
            css.*,
            cs.class_id,
            cs.subject_id,
            cs.teacher_id,
            s.name as subject_name,
            s.code as subject_code,
            CONCAT(t.first_name, ' ', t.last_name) as teacher_name
        FROM class_subject_schedule css
        LEFT JOIN class_subjects cs ON css.class_subject_id = cs.id
        LEFT JOIN subjects s ON cs.subject_id = s.id
        LEFT JOIN teachers t ON cs.teacher_id = t.id
        WHERE cs.class_id = ?
        ORDER BY 
            FIELD(css.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            css.start_time
    ");
    $query->bind_param("i", $class_id);
    $query->execute();
    $result = $query->get_result();
    
    $timetable = [];
    while ($row = $result->fetch_assoc()) {
        $timetable[$row['day_of_week']][] = $row;
    }
    
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    ?>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Waqtiga</th>
                    <?php foreach ($days as $day): ?>
                        <th><?php echo $day; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $time_slots = [];
                foreach ($days as $day) {
                    if (isset($timetable[$day])) {
                        foreach ($timetable[$day] as $subject) {
                            $time_key = $subject['start_time'] . '-' . $subject['end_time'];
                            $time_slots[$time_key] = $time_key;
                        }
                    }
                }
                ksort($time_slots);
                
                foreach ($time_slots as $time_slot):
                    list($start_time, $end_time) = explode('-', $time_slot);
                ?>
                    <tr>
                        <td class="bg-light">
                            <strong><?php echo date('h:i A', strtotime($start_time)); ?></strong><br>
                            <small>ilaa <?php echo date('h:i A', strtotime($end_time)); ?></small>
                        </td>
                        <?php foreach ($days as $day): ?>
                            <td>
                                <?php if (isset($timetable[$day])): ?>
                                    <?php foreach ($timetable[$day] as $subject): ?>
                                        <?php if ($subject['start_time'] == $start_time && $subject['end_time'] == $end_time): ?>
                                            <div class="p-2 bg-primary text-white rounded mb-1">
                                                <strong><?php echo htmlspecialchars($subject['subject_name']); ?></strong><br>
                                                <small>
                                                    <i class="bi bi-person"></i> 
                                                    <?php echo htmlspecialchars($subject['teacher_name'] ?? 'N/A'); ?>
                                                </small><br>
                                                <small>
                                                    <i class="bi bi-code-slash"></i> 
                                                    <?php echo htmlspecialchars($subject['subject_code']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (count($time_slots) === 0): ?>
        <div class="alert alert-info">
            Ma jiro wax jadwal ah.
        </div>
    <?php endif; ?>
    <?php
} else {
    ?>
    <div class="alert alert-warning">
        Arday fasal lama siinin ilaa hada. fadlan la xiriir qeybta adeega macaamisha...
    </div>
    <?php
}
?>