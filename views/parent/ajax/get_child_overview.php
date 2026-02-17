<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$child_id = $_POST['child_id'];

$query = $conn->prepare("
    SELECT 
        s.*,
        cs.class_id,
        c.name as class_name,
        sec.name as section_name,
        DATE_FORMAT(s.dob, '%M %d, %Y') as dob_formatted,
        TIMESTAMPDIFF(YEAR, s.dob, CURDATE()) as age
    FROM students s
    LEFT JOIN class_students cs ON s.id = cs.student_id AND cs.is_active = 1
    LEFT JOIN classes c ON cs.class_id = c.id
    LEFT JOIN sections sec ON c.section_id = sec.id
    WHERE s.id = ?
");
$query->bind_param("i", $child_id);
$query->execute();
$child = $query->get_result()->fetch_assoc();

if ($child) {
    ?>
    <div class="row">
        <div class="col-md-3 text-center">
<?php 
$first_letter = strtoupper(substr($child['name'], 0, 1));
?>
<div class="child-avatar mb-3 d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:100px; height:100px; font-size:40px;">
    <?php echo $first_letter; ?>
</div>
            <h5><?php echo htmlspecialchars($child['name']); ?></h5>
            <p class="text-muted">ID-ga ardayga: #<?php echo str_pad($child['id'], 6, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Tarrikhda Dhalashada:</th>
                            <td><?php echo $child['dob_formatted']; ?> (Age: <?php echo $child['age']; ?>)</td>
                        </tr>
                        <tr>
                            <th>Jinsiyada:</th>
                            <td><?php echo ucfirst($child['gender']); ?></td>
                        </tr>
                        <tr>
                            <th>Fasalka:</th>
                            <td><?php echo $child['class_name'] ?? 'Not Assigned'; ?></td>
                        </tr>
                        <tr>
                            <th>Section:</th>
                            <td><?php echo $child['section_name'] ?? 'Not Assigned'; ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Tarrikhda Diwaanka:</th>
                            <td><?php echo date('M d, Y', strtotime($child['admission_date'])); ?></td>
                        </tr>
                        <tr>
                            <th>Goobta:</th>
                            <td><?php echo htmlspecialchars($child['address'] ?: 'Not provided'); ?></td>
                        </tr>
                        <tr>
                            <th>Waalidka/Masuulka:</th>
                            <td><?php echo htmlspecialchars($child['guardian_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Lambarka Waalidka/Masuulka:</th>
                            <td><?php echo htmlspecialchars($child['guardian_phone']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>