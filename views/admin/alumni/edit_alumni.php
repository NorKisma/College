<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Edit Alumni - {student_name} - Student Management System',
        'dashboard' => 'Dashboard',
        'manage_alumni' => 'Manage Alumni',
        'edit_alumni' => 'Edit Alumni',
        'alumni_id' => 'Alumni ID',
        'section' => 'Section',
        'class' => 'Class',
        'basic_information' => 'Basic Information',
        'student_id' => 'Student ID',
        'full_name' => 'Full Name',
        'date_of_birth' => 'Date of Birth',
        'gender' => 'Gender',
        'graduation_details' => 'Graduation Details',
        'graduation_date' => 'Graduation Date',
        'graduation_reason' => 'Graduation Reason',
        'exam_performance' => 'Exam Performance',
        'attendance_performance' => 'Attendance Performance',
        'both_exam_attendance' => 'Both Exam & Attendance',
        'exam_score' => 'Exam Score',
        'optional' => 'Optional',
        'enter_final_exam_score' => 'Enter final exam score',
        'attendance_percentage' => 'Attendance Percentage',
        'enter_attendance_percentage' => 'Enter attendance percentage',
        'guardian_information' => 'Guardian Information',
        'guardian_name' => 'Guardian Name',
        'guardian_phone' => 'Guardian Phone',
        'guardian_email' => 'Guardian Email',
        'address' => 'Address',
        'update_notes' => 'Update Notes',
        'change_description' => 'Change Description',
        'describe_changes' => 'Describe the changes you\'re making...',
        'note_saved_history' => 'This note will be saved in the history log.',
        'change_history' => 'Change History',
        'system' => 'System',
        'alumni_record_updated' => 'Alumni record updated',
        'no_change_history' => 'No change history available for this alumni record.',
        'save_changes' => 'Save Changes',
        'back_to_list' => 'Back to List',
        'view_profile' => 'View Profile',
        'record_updated_success' => 'Alumni record updated successfully!',
        'error_updating' => 'Error updating record',
        'confirm_removal' => 'Confirm Removal',
        'remove_confirmation' => 'Are you sure you want to remove',
        'from_alumni_list' => 'from the alumni list?',
        'action_cannot_undone' => 'This action cannot be undone. The student will no longer appear in alumni reports.',
        'reason_for_removal' => 'Reason for removal:',
        'please_provide_reason' => 'Please provide a reason...',
        'cancel' => 'Cancel',
        'confirm_removal_btn' => 'Confirm Removal',
        'provide_reason' => 'Please provide a reason for removal.',
        'absolutely_sure' => 'Are you absolutely sure? This action cannot be undone.',
        'transfer_alumni' => 'Transfer Alumni',
        'transfer_to_same_section' => 'Transfer to a different class in the same section',
        'transfer_different_section' => 'Transfer to a different section and class',
        'select_new_section' => 'Select New Section',
        'select_section' => 'Select Section',
        'select_new_class' => 'Select New Class',
        'select_class' => 'Select Class',
        'transfer_alumni_btn' => 'Transfer Alumni',
        'please_select_class' => 'Please select a new class.',
        'please_select_section' => 'Please select a new section.',
        'confirm_transfer' => 'Transfer this alumni to the selected class?',
        'transferred_success' => 'Alumni transferred successfully!',
        'error' => 'Error'
    ],
    2 => [ // Arabic
        'page_title' => 'تعديل الخريج - {student_name} - نظام إدارة الطلاب',
        'dashboard' => 'لوحة التحكم',
        'manage_alumni' => 'إدارة الخريجين',
        'edit_alumni' => 'تعديل الخريج',
        'alumni_id' => 'رقم الخريج',
        'section' => 'القسم',
        'class' => 'الصف',
        'basic_information' => 'المعلومات الأساسية',
        'student_id' => 'رقم الطالب',
        'full_name' => 'الاسم الكامل',
        'date_of_birth' => 'تاريخ الميلاد',
        'gender' => 'الجنس',
        'graduation_details' => 'تفاصيل التخرج',
        'graduation_date' => 'تاريخ التخرج',
        'graduation_reason' => 'سبب التخرج',
        'exam_performance' => 'أداء الامتحان',
        'attendance_performance' => 'أداء الحضور',
        'both_exam_attendance' => 'كل من الامتحان والحضور',
        'exam_score' => 'نتيجة الامتحان',
        'optional' => 'اختياري',
        'enter_final_exam_score' => 'أدخل نتيجة الامتحان النهائي',
        'attendance_percentage' => 'نسبة الحضور',
        'enter_attendance_percentage' => 'أدخل نسبة الحضور',
        'guardian_information' => 'معلومات ولي الأمر',
        'guardian_name' => 'اسم ولي الأمر',
        'guardian_phone' => 'هاتف ولي الأمر',
        'guardian_email' => 'بريد ولي الأمر',
        'address' => 'العنوان',
        'update_notes' => 'ملاحظات التحديث',
        'change_description' => 'وصف التغيير',
        'describe_changes' => 'صف التغييرات التي تجريها...',
        'note_saved_history' => 'سيتم حفظ هذه الملاحظة في سجل التاريخ.',
        'change_history' => 'سجل التغييرات',
        'system' => 'النظام',
        'alumni_record_updated' => 'تم تحديث سجل الخريج',
        'no_change_history' => 'لا يوجد سجل تغييرات متاح لسجل الخريج هذا.',
        'save_changes' => 'حفظ التغييرات',
        'back_to_list' => 'العودة إلى القائمة',
        'view_profile' => 'عرض الملف الشخصي',
        'record_updated_success' => 'تم تحديث سجل الخريج بنجاح!',
        'error_updating' => 'خطأ في تحديث السجل',
        'confirm_removal' => 'تأكيد الحذف',
        'remove_confirmation' => 'هل أنت متأكد من رغبتك في حذف',
        'from_alumni_list' => 'من قائمة الخريجين؟',
        'action_cannot_undone' => 'لا يمكن التراجع عن هذا الإجراء. لن يظهر الطالب في تقارير الخريجين.',
        'reason_for_removal' => 'سبب الحذف:',
        'please_provide_reason' => 'يرجى تقديم سبب...',
        'cancel' => 'إلغاء',
        'confirm_removal_btn' => 'تأكيد الحذف',
        'provide_reason' => 'يرجى تقديم سبب للحذف.',
        'absolutely_sure' => 'هل أنت متأكد تماماً؟ لا يمكن التراجع عن هذا الإجراء.',
        'transfer_alumni' => 'نقل الخريج',
        'transfer_to_same_section' => 'نقل إلى فصل مختلف في نفس القسم',
        'transfer_different_section' => 'نقل إلى قسم وفصل مختلفين',
        'select_new_section' => 'اختر القسم الجديد',
        'select_section' => 'اختر القسم',
        'select_new_class' => 'اختر الصف الجديد',
        'select_class' => 'اختر الصف',
        'transfer_alumni_btn' => 'نقل الخريج',
        'please_select_class' => 'يرجى اختيار فصل جديد.',
        'please_select_section' => 'يرجى اختيار قسم جديد.',
        'confirm_transfer' => 'نقل هذا الخريج إلى الفصل المحدد؟',
        'transferred_success' => 'تم نقل الخريج بنجاح!',
        'error' => 'خطأ'
    ]
];

function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

if (empty($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

// theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;
$is_dark = ($theme === 0);

$alumni_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$alumni_id) {
    header('Location: alumni.php');
    exit;
}

// Get alumni details
$query = $conn->prepare("
    SELECT 
        a.*,
        s.id as student_id,
        s.name as student_name,
        s.dob,
        s.gender,
        s.address,
        s.guardian_name,
        s.guardian_phone,
        s.guardian_email,
        c.name as class_name,
        c.id as class_id,
        sec.name as section_name,
        sec.id as section_id
    FROM alumni a
    INNER JOIN students s ON a.student_id = s.id
    INNER JOIN classes c ON a.class_id = c.id
    INNER JOIN sections sec ON a.section_id = sec.id
    WHERE a.id = ?
");
$query->bind_param("i", $alumni_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    header('Location: alumni.php');
    exit;
}

$alumni = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $graduation_date = $_POST['graduation_date'];
    $graduation_reason = $conn->real_escape_string($_POST['graduation_reason']);
    $exam_score = !empty($_POST['exam_score']) ? floatval($_POST['exam_score']) : NULL;
    $attendance_percentage = !empty($_POST['attendance_percentage']) ? floatval($_POST['attendance_percentage']) : NULL;
    $notes = $conn->real_escape_string($_POST['notes'] ?? '');
    
    // Update query
    $update_query = $conn->prepare("
        UPDATE alumni 
        SET graduation_date = ?,
            graduation_reason = ?,
            exam_score = ?,
            attendance_percentage = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    
    $update_query->bind_param(
        "ssddi",
        $graduation_date,
        $graduation_reason,
        $exam_score,
        $attendance_percentage,
        $alumni_id
    );
    
    if ($update_query->execute()) {
        // Add to history if notes provided
        if (!empty($notes)) {
            $history_query = $conn->prepare("
                INSERT INTO alumni_history (alumni_id, changed_by, changes_description, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $history_query->bind_param("iis", $alumni_id, $_SESSION['user_id'], $notes);
            $history_query->execute();
        }
        
        $message = t('record_updated_success');
        $message_type = "success";
        
        // Refresh data
        $query->execute();
        $result = $query->get_result();
        $alumni = $result->fetch_assoc();
    } else {
        $message = t('error_updating') . ": " . $conn->error;
        $message_type = "error";
    }
}

// Get sections for possible transfer
$sections = $conn->query("SELECT id, name FROM sections ORDER BY name");

// Get classes for selected section
$classes = [];
if ($alumni['section_id']) {
    $class_query = $conn->query("SELECT id, name FROM classes WHERE section_id = {$alumni['section_id']} ORDER BY name");
    $classes = $class_query->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title', ['student_name' => htmlspecialchars($alumni['student_name'])]) ?></title>
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?= $is_dark ? 'dark' : 'light' ?>">
    <div class="container">
        <div class="header-section">
            <div class="breadcrumb">
                <a href="../dashboard.php"><i class="fas fa-home"></i> <?= t('dashboard') ?></a> &raquo;
                <a href="manage_alumni.php"><i class="fas fa-user-graduate"></i> <?= t('manage_alumni') ?></a> &raquo;
                <span><i class="fas fa-edit"></i> <?= t('edit_alumni') ?></span>
            </div>
            <h1><i class="fas fa-user-edit"></i> <?= t('edit_alumni') ?>: <?= htmlspecialchars($alumni['student_name']) ?></h1>
            <p><?= t('alumni_id') ?>: #<?= $alumni['student_id'] ?> | <?= t('section') ?>: <?= htmlspecialchars($alumni['section_name']) ?> | <?= t('class') ?>: <?= htmlspecialchars($alumni['class_name']) ?></p>
        </div>

        <div class="content-wrapper">
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="editAlumniForm">
                <div class="form-section">
                    <!-- Left Column: Basic Information -->
                    <div>
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-info-circle"></i> <?= t('basic_information') ?>
                            </div>
                            <div class="card-body">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label"><?= t('student_id') ?></div>
                                        <div class="info-value">#<?= $alumni['student_id'] ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><?= t('full_name') ?></div>
                                        <div class="info-value"><?= htmlspecialchars($alumni['student_name']) ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><?= t('date_of_birth') ?></div>
                                        <div class="info-value"><?= $alumni['dob'] ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><?= t('gender') ?></div>
                                        <div class="info-value"><?= ucfirst($alumni['gender']) ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><?= t('section') ?></div>
                                        <div class="info-value"><?= htmlspecialchars($alumni['section_name']) ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><?= t('class') ?></div>
                                        <div class="info-value"><?= htmlspecialchars($alumni['class_name']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-graduation-cap"></i> <?= t('graduation_details') ?>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label" for="graduation_date"><?= t('graduation_date') ?> *</label>
                                    <input type="date" 
                                           id="graduation_date" 
                                           name="graduation_date" 
                                           class="form-control" 
                                           value="<?= $alumni['graduation_date'] ?>" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="graduation_reason"><?= t('graduation_reason') ?> *</label>
                                    <select id="graduation_reason" name="graduation_reason" class="form-control" required>
                                        <option value="exam_performance" <?= $alumni['graduation_reason'] == 'exam_performance' ? 'selected' : '' ?>><?= t('exam_performance') ?></option>
                                        <option value="attendance_performance" <?= $alumni['graduation_reason'] == 'attendance_performance' ? 'selected' : '' ?>><?= t('attendance_performance') ?></option>
                                        <option value="both" <?= $alumni['graduation_reason'] == 'both' ? 'selected' : '' ?>><?= t('both_exam_attendance') ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="exam_score"><?= t('exam_score') ?> (<?= t('optional') ?>)</label>
                                    <input type="number" 
                                           id="exam_score" 
                                           name="exam_score" 
                                           class="form-control" 
                                           step="0.01" 
                                           min="0" 
                                           value="<?= $alumni['exam_score'] ?>"
                                           placeholder="<?= t('enter_final_exam_score') ?>">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="attendance_percentage"><?= t('attendance_percentage') ?> (<?= t('optional') ?>)</label>
                                    <input type="number" 
                                           id="attendance_percentage" 
                                           name="attendance_percentage" 
                                           class="form-control" 
                                           step="0.01" 
                                           min="0" 
                                           max="100" 
                                           value="<?= $alumni['attendance_percentage'] ?>"
                                           placeholder="<?= t('enter_attendance_percentage') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Guardian Info & History -->
                    <div>
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-users"></i> <?= t('guardian_information') ?>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label"><?= t('guardian_name') ?></label>
                                    <input type="text" 
                                           class="form-control readonly-field" 
                                           value="<?= htmlspecialchars($alumni['guardian_name']) ?>" 
                                           readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label"><?= t('guardian_phone') ?></label>
                                    <input type="text" 
                                           class="form-control readonly-field" 
                                           value="<?= htmlspecialchars($alumni['guardian_phone']) ?>" 
                                           readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label"><?= t('guardian_email') ?></label>
                                    <input type="email" 
                                           class="form-control readonly-field" 
                                           value="<?= htmlspecialchars($alumni['guardian_email']) ?>" 
                                           readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label"><?= t('address') ?></label>
                                    <textarea class="form-control readonly-field" rows="3" readonly><?= htmlspecialchars($alumni['address']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-edit"></i> <?= t('update_notes') ?>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label" for="notes"><?= t('change_description') ?> (<?= t('optional') ?>)</label>
                                    <textarea id="notes" 
                                              name="notes" 
                                              class="form-control textarea-full" 
                                              rows="4"
                                              placeholder="<?= t('describe_changes') ?>"></textarea>
                                    <small class="text-muted"><?= t('note_saved_history') ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Change History -->
                        <?php
                        // Instead of alumni_history, just use the alumni table
                        $history_query = $conn->prepare("
                            SELECT *, 'System' as changed_by_name
                            FROM alumni
                            WHERE id = ?
                            ORDER BY updated_at DESC
                        ");

                        if (!$history_query) {
                            die("Prepare failed: " . $conn->error);
                        }

                        $history_query->bind_param("i", $alumni_id);
                        $history_query->execute();
                        $history_result = $history_query->get_result();
                        ?>

                        <div class="history-section">
                            <h4><i class="fas fa-history"></i> <?= t('change_history') ?></h4>
                            <?php if ($history_result->num_rows > 0): ?>
                                <?php while ($history = $history_result->fetch_assoc()): ?>
                                    <div class="history-item">
                                        <div class="meta">
                                            <span><i class="fas fa-user"></i> <?= t('system') ?></span>
                                            <span><i class="fas fa-clock"></i> <?= date('Y-m-d H:i', strtotime($history['updated_at'] ?? $history['created_at'])) ?></span>
                                        </div>
                                        <div class="changes">
                                            <i class="fas fa-sticky-note"></i> <?= t('alumni_record_updated') ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="no-history">
                                    <i class="fas fa-history fa-2x"></i>
                                    <p><?= t('no_change_history') ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> <?= t('save_changes') ?>
                    </button>
                    <a href="alumni.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> <?= t('back_to_list') ?>
                    </a>
                    <button type="button" class="btn btn-primary" onclick="window.open('alumni_profile.php?id=<?= $alumni['student_id'] ?>', '_blank')">
                        <i class="fas fa-external-link-alt"></i> <?= t('view_profile') ?>
                    </button>
                </div>
            </form>

            <!-- Danger Zone -->
           
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger"></i> <?= t('confirm_removal') ?></h5>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p><?= t('remove_confirmation') ?> <strong><?= htmlspecialchars($alumni['student_name']) ?></strong> <?= t('from_alumni_list') ?></p>
                <p><?= t('action_cannot_undone') ?></p>
                
                <div class="form-group">
                    <label for="delete_reason"><?= t('reason_for_removal') ?></label>
                    <textarea id="delete_reason" class="form-control" rows="3" placeholder="<?= t('please_provide_reason') ?>"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal()"><?= t('cancel') ?></button>
                <button type="button" class="btn btn-danger" onclick="deleteAlumni()"><?= t('confirm_removal_btn') ?></button>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function deleteAlumni() {
            const reason = document.getElementById('delete_reason').value.trim();
            if (!reason) {
                alert('<?= t('provide_reason') ?>');
                return;
            }

            if (confirm('<?= t('absolutely_sure') ?>')) {
                const formData = new FormData();
                formData.append('alumni_id', <?= $alumni_id ?>);
                formData.append('remove_reason', reason);
                formData.append('remove_alumni', '1');

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = 'alumni.php';
                    }
                });
            }
        }

        function transferAlumni() {
            const transferType = document.querySelector('input[name="transfer_type"]:checked').value;
            const newClass = document.getElementById('new_class').value;
            const newSection = document.getElementById('new_section').value;

            if (!newClass) {
                alert('<?= t('please_select_class') ?>');
                return;
            }

            if (transferType === 'different' && !newSection) {
                alert('<?= t('please_select_section') ?>');
                return;
            }

            if (confirm('<?= t('confirm_transfer') ?>')) {
                const formData = new FormData();
                formData.append('alumni_id', <?= $alumni_id ?>);
                formData.append('new_class_id', newClass);
                formData.append('new_section_id', newSection || <?= $alumni['section_id'] ?>);
                formData.append('transfer_alumni', '1');

                fetch('ajax_transfer_alumni.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('<?= t('transferred_success') ?>');
                        window.location.reload();
                    } else {
                        alert('<?= t('error') ?>: ' + data.message);
                    }
                });
            }
        }

        // Handle transfer type changes
        document.querySelectorAll('input[name="transfer_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const newSectionSelect = document.getElementById('new_section');
                const newClassSelect = document.getElementById('new_class');
                
                if (this.value === 'different') {
                    newSectionSelect.disabled = false;
                    newClassSelect.innerHTML = '<option value=""><?= t('select_class') ?></option>';
                } else {
                    newSectionSelect.disabled = true;
                    newSectionSelect.value = '';
                    
                    // Reload classes for current section
                    fetch('ajax_get_classes.php?section=<?= $alumni['section_id'] ?>')
                        .then(response => response.json())
                        .then(classes => {
                            newClassSelect.innerHTML = '<option value=""><?= t('select_class') ?></option>';
                            classes.forEach(classItem => {
                                const option = document.createElement('option');
                                option.value = classItem.id;
                                option.textContent = classItem.name;
                                if (classItem.id == <?= $alumni['class_id'] ?>) {
                                    option.selected = true;
                                }
                                newClassSelect.appendChild(option);
                            });
                        });
                }
            });
        });

        // Load classes when new section is selected
        document.getElementById('new_section').addEventListener('change', function() {
            const sectionId = this.value;
            const classSelect = document.getElementById('new_class');
            
            if (sectionId) {
                fetch('ajax_get_classes.php?section=' + sectionId)
                    .then(response => response.json())
                    .then(classes => {
                        classSelect.innerHTML = '<option value=""><?= t('select_class') ?></option>';
                        classes.forEach(classItem => {
                            const option = document.createElement('option');
                            option.value = classItem.id;
                            option.textContent = classItem.name;
                            classSelect.appendChild(option);
                        });
                    });
            } else {
                classSelect.innerHTML = '<option value=""><?= t('select_class') ?></option>';
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeModal();
            }
        };
    </script>
</body>
</html>

  <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        body.light {
            background-color: #f5f5f5;
            color: #333;
        }

        body.dark {
            background-color: #1a1a1a;
            color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        body.dark .container > div {
            background-color: #2d2d2d;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        body.dark .form-control,
        body.dark .table,
        body.dark .btn-outline-secondary,
        body.dark .card,
        body.dark .history-item {
            background-color: #3d3d3d;
            color: #fff;
            border-color: #555;
        }

        body.dark .table {
            background-color: #2d2d2d;
        }

        body.dark .card-header {
            background-color: #3d3d3d;
            border-bottom-color: #555;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }

        .header-section h1 {
            margin-bottom: 10px;
            font-size: 28px;
        }

        .header-section .breadcrumb {
            display: flex;
            gap: 10px;
            font-size: 14px;
            opacity: 0.9;
        }

        .header-section .breadcrumb a {
            color: white;
            text-decoration: none;
        }

        .header-section .breadcrumb a:hover {
            text-decoration: underline;
        }

        .content-wrapper {
            background-color: #fff;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        body.dark .content-wrapper {
            background-color: #2d2d2d;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        body.dark .alert-success {
            background-color: #155724;
            color: #d4edda;
            border-color: #0c3d1a;
        }

        body.dark .alert-error {
            background-color: #721c24;
            color: #f8d7da;
            border-color: #5a151b;
        }

        .form-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .form-section {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        body.dark .form-label {
            color: #ccc;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }

        .readonly-field {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        body.dark .readonly-field {
            background-color: #3d3d3d;
            color: #aaa;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-primary { background-color: #007bff; color: white; }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #1e7e34;
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        body.dark .form-actions {
            border-top-color: #555;
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        body.dark .info-label {
            color: #aaa;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
        }

        .history-section {
            margin-top: 40px;
        }

        .history-item {
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border-radius: 0 6px 6px 0;
        }

        .history-item .changes {
            font-style: italic;
            color: #6c757d;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #dee2e6;
        }

        body.dark .history-item .changes {
            color: #aaa;
            border-top-color: #555;
        }

        .history-item .meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        body.dark .history-item .meta {
            color: #aaa;
        }

        .no-history {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }

        .danger-zone {
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 20px;
            margin-top: 40px;
            background-color: #fff5f5;
        }

        body.dark .danger-zone {
            background-color: #3d2d2d;
            border-color: #dc3545;
        }

        .danger-zone h4 {
            color: #dc3545;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .danger-zone p {
            color: #721c24;
            margin-bottom: 15px;
        }

        body.dark .danger-zone p {
            color: #f8d7da;
        }

        .textarea-full {
            min-height: 100px;
            resize: vertical;
        }

        .transfer-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        body.dark .transfer-section {
            background-color: #3d3d3d;
        }

        .transfer-section h5 {
            margin-bottom: 15px;
            color: #6c757d;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>