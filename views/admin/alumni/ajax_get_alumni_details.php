<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'student_name' => 'Student Name',
        'date_of_birth' => 'Date of Birth',
        'gender' => 'Gender',
        'section_class' => 'Section & Class',
        'graduation_date' => 'Graduation Date',
        'graduation_reason' => 'Graduation Reason',
        'exam_score' => 'Exam Score',
        'attendance_percentage' => 'Attendance %',
        'guardian_name' => 'Guardian Name',
        'guardian_phone' => 'Guardian Phone',
        'guardian_email' => 'Guardian Email',
        'address' => 'Address',
        'alumni_since' => 'Alumni Since',
        'details_not_found' => 'Alumni details not found.',
        'exam_performance' => 'Exam Performance',
        'attendance_performance' => 'Attendance Performance',
        'both' => 'Both'
    ],
    2 => [ // Arabic
        'student_name' => 'اسم الطالب',
        'date_of_birth' => 'تاريخ الميلاد',
        'gender' => 'الجنس',
        'section_class' => 'القسم والصف',
        'graduation_date' => 'تاريخ التخرج',
        'graduation_reason' => 'سبب التخرج',
        'exam_score' => 'نتيجة الامتحان',
        'attendance_percentage' => 'نسبة الحضور',
        'guardian_name' => 'اسم ولي الأمر',
        'guardian_phone' => 'هاتف ولي الأمر',
        'guardian_email' => 'بريد ولي الأمر',
        'address' => 'العنوان',
        'alumni_since' => 'خريج منذ',
        'details_not_found' => 'لم يتم العثور على تفاصيل الخريج.',
        'exam_performance' => 'أداء الامتحان',
        'attendance_performance' => 'أداء الحضور',
        'both' => 'كلاهما'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

// Helper function to translate graduation reason
function translateReason($reason) {
    switch($reason) {
        case 'exam_performance': return t('exam_performance');
        case 'attendance_performance': return t('attendance_performance');
        case 'both': return t('both');
        default: return ucfirst(str_replace('_', ' ', $reason));
    }
}

if (empty($_SESSION['user_id'])) {
    exit;
}

$alumni_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($alumni_id) {
    $query = $conn->prepare("
        SELECT 
            a.*,
            s.name as student_name,
            s.dob,
            s.gender,
            s.address,
            s.guardian_name,
            s.guardian_phone,
            s.guardian_email,
            c.name as class_name,
            sec.name as section_name
        FROM alumni a
        INNER JOIN students s ON a.student_id = s.id
        INNER JOIN classes c ON a.class_id = c.id
        INNER JOIN sections sec ON a.section_id = sec.id
        WHERE a.id = ?
    ");
    $query->bind_param("i", $alumni_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($alumni = $result->fetch_assoc()):
        ?>
        <style>
            .details-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
            
            .detail-item {
                margin-bottom: 10px;
            }
            
            .detail-label {
                font-weight: bold;
                color: #666;
                font-size: 12px;
                text-transform: uppercase;
            }
            
            .detail-value {
                font-size: 14px;
            }
            
            .badge {
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
            }
            
            .badge-success { background-color: #28a745; color: white; }
            .badge-warning { background-color: #ffc107; color: #212529; }
            .badge-info { background-color: #17a2b8; color: white; }
        </style>
        
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-label"><?= t('student_name') ?></div>
                <div class="detail-value"><?= htmlspecialchars($alumni['student_name']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('date_of_birth') ?></div>
                <div class="detail-value"><?= $alumni['dob'] ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('gender') ?></div>
                <div class="detail-value"><?= ucfirst($alumni['gender']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('section_class') ?></div>
                <div class="detail-value">
                    <?= htmlspecialchars($alumni['section_name']) ?> / <?= htmlspecialchars($alumni['class_name']) ?>
                </div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('graduation_date') ?></div>
                <div class="detail-value"><?= $alumni['graduation_date'] ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('graduation_reason') ?></div>
                <div class="detail-value">
                    <?php
                    $badge_class = '';
                    switch($alumni['graduation_reason']) {
                        case 'exam_performance': $badge_class = 'badge-success'; break;
                        case 'attendance_performance': $badge_class = 'badge-warning'; break;
                        case 'both': $badge_class = 'badge-info'; break;
                    }
                    ?>
                    <span class="badge <?= $badge_class ?>">
                        <?= translateReason($alumni['graduation_reason']) ?>
                    </span>
                </div>
            </div>
            
            <?php if ($alumni['exam_score']): ?>
            <div class="detail-item">
                <div class="detail-label"><?= t('exam_score') ?></div>
                <div class="detail-value"><?= number_format($alumni['exam_score'], 2) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($alumni['attendance_percentage']): ?>
            <div class="detail-item">
                <div class="detail-label"><?= t('attendance_percentage') ?></div>
                <div class="detail-value"><?= number_format($alumni['attendance_percentage'], 2) ?>%</div>
            </div>
            <?php endif; ?>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('guardian_name') ?></div>
                <div class="detail-value"><?= htmlspecialchars($alumni['guardian_name']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('guardian_phone') ?></div>
                <div class="detail-value"><?= htmlspecialchars($alumni['guardian_phone']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('guardian_email') ?></div>
                <div class="detail-value"><?= htmlspecialchars($alumni['guardian_email']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('address') ?></div>
                <div class="detail-value"><?= htmlspecialchars($alumni['address']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?= t('alumni_since') ?></div>
                <div class="detail-value"><?= date('F j, Y, g:i a', strtotime($alumni['created_at'])) ?></div>
            </div>
        </div>
        <?php
    else:
        echo '<p class="text-muted">' . t('details_not_found') . '</p>';
    endif;
}
?>