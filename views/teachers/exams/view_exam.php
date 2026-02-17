<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)$_SESSION['user_id'];
$exam_id    = (int)($_GET['id'] ?? 0);

if ($exam_id <= 0) {
    die("Invalid exam ID.");
}

/* ================= TRANSLATIONS ================= */
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Exam Dashboard | Teacher Panel',
        'back' => 'Back',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'enter_edit_marks' => 'Enter/Edit Marks',
        'add_subjects' => 'Add Subjects',
        'more_actions' => 'More Actions',
        'duplicate_exam' => 'Duplicate Exam',
        'share_results' => 'Share Results',
        'archive_exam' => 'Archive Exam',
        'subjects' => 'Subjects',
        'configured' => 'configured',
        'students' => 'Students',
        'with_results' => 'with results',
        'average_marks' => 'Average Marks',
        'pass_rate' => 'pass rate',
        'days_remaining' => 'Days Remaining',
        'ends' => 'Ends',
        'overview' => 'Overview',
        'subjects_tab' => 'Subjects',
        'results' => 'Results',
        'analytics' => 'Analytics',
        'exam_overview' => 'Exam Overview',
        'marks_entry' => 'Marks Entry',
        'marks_description' => 'Enter and manage student marks for this exam.',
        'manage_subjects' => 'Manage Subjects',
        'subjects_description' => 'Add or remove subjects from this exam.',
        'exam_timeline' => 'Exam Timeline',
        'exam_created' => 'Exam Created',
        'exam_scheduled' => 'Exam was created and scheduled',
        'subjects_added' => 'Subjects Added',
        'subjects_configured' => 'subjects configured',
        'marks_entry_progress' => 'Marks Entry',
        'results_entered' => 'results entered so far',
        'results_publication' => 'Results Publication',
        'scheduled_completion' => 'Scheduled after exam completion',
        'quick_stats' => 'Quick Stats',
        'passed' => 'Passed',
        'failed' => 'Failed',
        'absent' => 'Absent',
        'present' => 'Present',
        'exam_subjects' => 'Exam Subjects',
        'max_marks' => 'Max Marks',
        'pass_marks' => 'Pass Marks',
        'subject_summary' => 'Subject Summary',
        'total_subjects' => 'Total Subjects',
        'total_max_marks' => 'Total Max Marks',
        'total_pass_marks' => 'Total Pass Marks',
        'no_subjects_added' => 'No Subjects Added',
        'no_subjects_message' => 'Add subjects to start entering marks for this exam',
        'student_results' => 'Student Results',
        'export' => 'Export',
        'print' => 'Print',
        'student' => 'Student',
        'marks_obtained' => 'Marks Obtained',
        'status' => 'Status',
        'grade' => 'Grade',
        'remarks' => 'Remarks',
        'actions' => 'Actions',
        'no_remarks' => 'No remarks',
        'view_details' => 'View Details',
        'edit_result' => 'Edit Result',
        'delete' => 'Delete',
        'total_results' => 'Total Results',
        'students_passed' => 'Students Passed',
        'students_failed' => 'Students Failed',
        'no_results_entered' => 'No Results Entered',
        'no_results_message' => 'Enter student marks to view results for this exam',
        'enter_marks' => 'Enter Marks',
        'exam_analytics' => 'Exam Analytics',
        'attendance_overview' => 'Attendance Overview',
        'top_performers' => 'Top Performers',
        'subject_performance' => 'Subject Performance',
        'average_marks_label' => 'Average Marks',
        'section' => 'Section',
        'complete' => 'Complete',
        'active' => 'Active',
        'completed' => 'Completed',
        'upcoming' => 'Upcoming',
        'invalid_exam' => 'Invalid exam ID.',
        'exam_not_found' => 'Exam not found or access denied.',
        'complete_lower' => 'complete',
        'exporting_results' => 'Exporting results...',
        'no_data_available' => 'No data available',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'entries' => 'entries',
        'filtered_from' => 'filtered from',
        'total_entries' => 'total entries',
        'no_matching_records' => 'No matching records found',
        'first' => 'First',
        'last' => 'Last',
        'next' => 'Next',
        'previous' => 'Previous',
        'search' => 'Search'
    ],
    2 => [ // Arabic
        'page_title' => 'لوحة تحكم الامتحان | لوحة المعلم',
        'back' => 'رجوع',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'enter_edit_marks' => 'إدخال/تعديل الدرجات',
        'add_subjects' => 'إضافة مواد',
        'more_actions' => 'المزيد من الإجراءات',
        'duplicate_exam' => 'نسخ الامتحان',
        'share_results' => 'مشاركة النتائج',
        'archive_exam' => 'أرشفة الامتحان',
        'subjects' => 'المواد',
        'configured' => 'مضبوط',
        'students' => 'الطلاب',
        'with_results' => 'بنتائج',
        'average_marks' => 'متوسط الدرجات',
        'pass_rate' => 'معدل النجاح',
        'days_remaining' => 'الأيام المتبقية',
        'ends' => 'ينتهي',
        'overview' => 'نظرة عامة',
        'subjects_tab' => 'المواد',
        'results' => 'النتائج',
        'analytics' => 'التحليلات',
        'exam_overview' => 'نظرة عامة على الامتحان',
        'marks_entry' => 'إدخال الدرجات',
        'marks_description' => 'أدخل وادير درجات الطلاب لهذا الامتحان.',
        'manage_subjects' => 'إدارة المواد',
        'subjects_description' => 'أضف أو أزل المواد من هذا الامتحان.',
        'exam_timeline' => 'الجدول الزمني للامتحان',
        'exam_created' => 'تم إنشاء الامتحان',
        'exam_scheduled' => 'تم إنشاء وجدولة الامتحان',
        'subjects_added' => 'تمت إضافة المواد',
        'subjects_configured' => 'مادة مضبوطة',
        'marks_entry_progress' => 'إدخال الدرجات',
        'results_entered' => 'نتيجة تم إدخالها حتى الآن',
        'results_publication' => 'نشر النتائج',
        'scheduled_completion' => 'مجدول بعد اكتمال الامتحان',
        'quick_stats' => 'إحصائيات سريعة',
        'passed' => 'ناجح',
        'failed' => 'راسب',
        'absent' => 'غائب',
        'present' => 'حاضر',
        'exam_subjects' => 'مواد الامتحان',
        'max_marks' => 'الدرجة القصوى',
        'pass_marks' => 'درجة النجاح',
        'subject_summary' => 'ملخص المواد',
        'total_subjects' => 'إجمالي المواد',
        'total_max_marks' => 'إجمالي الدرجات القصوى',
        'total_pass_marks' => 'إجمالي درجات النجاح',
        'no_subjects_added' => 'لم تتم إضافة مواد',
        'no_subjects_message' => 'أضف مواد لبدء إدخال الدرجات لهذا الامتحان',
        'student_results' => 'نتائج الطلاب',
        'export' => 'تصدير',
        'print' => 'طباعة',
        'student' => 'الطالب',
        'marks_obtained' => 'الدرجات المتحصل عليها',
        'status' => 'الحالة',
        'grade' => 'التقدير',
        'remarks' => 'ملاحظات',
        'actions' => 'الإجراءات',
        'no_remarks' => 'لا توجد ملاحظات',
        'view_details' => 'عرض التفاصيل',
        'edit_result' => 'تعديل النتيجة',
        'delete' => 'حذف',
        'total_results' => 'إجمالي النتائج',
        'students_passed' => 'الطلاب الناجحين',
        'students_failed' => 'الطلاب الراسبين',
        'no_results_entered' => 'لم يتم إدخال نتائج',
        'no_results_message' => 'أدخل درجات الطلاب لعرض نتائج هذا الامتحان',
        'enter_marks' => 'أدخل الدرجات',
        'exam_analytics' => 'تحليلات الامتحان',
        'attendance_overview' => 'نظرة عامة على الحضور',
        'top_performers' => 'أفضل الأداء',
        'subject_performance' => 'أداء المواد',
        'average_marks_label' => 'متوسط الدرجات',
        'section' => 'القسم',
        'complete' => 'مكتمل',
        'active' => 'نشط',
        'completed' => 'مكتمل',
        'upcoming' => 'قادم',
        'invalid_exam' => 'رقم امتحان غير صالح.',
        'exam_not_found' => 'لم يتم العثور على الامتحان أو تم رفض الوصول.',
        'complete_lower' => 'مكتمل',
        'exporting_results' => 'جاري تصدير النتائج...',
        'no_data_available' => 'لا توجد بيانات متاحة',
        'showing' => 'عرض',
        'to' => 'إلى',
        'of' => 'من',
        'entries' => 'إدخالات',
        'filtered_from' => 'مفلتر من',
        'total_entries' => 'إجمالي الإدخالات',
        'no_matching_records' => 'لم يتم العثور على سجلات مطابقة',
        'first' => 'الأول',
        'last' => 'الأخير',
        'next' => 'التالي',
        'previous' => 'السابق',
        'search' => 'بحث'
    ],
    3 => [ // Somali
        'page_title' => 'Dashboard-ka Imtixaanka | Gudiga Macallinka',
        'back' => 'Dib u noqo',
        'start_date' => 'Taariikhda Bilowga',
        'end_date' => 'Taariikhda Dhamaadka',
        'enter_edit_marks' => 'Gali/Wax ka beddel Dhibcaha',
        'add_subjects' => 'Ku dar maad',
        'more_actions' => 'Ficillo Dheeraad ah',
        'duplicate_exam' => 'Ku qor Imtixaanka',
        'share_results' => 'Wadaag Natiijooyinka',
        'archive_exam' => 'Keydso Imtixaanka',
        'subjects' => 'Maadoyiinka',
        'configured' => 'la diyaariyay',
        'students' => 'Ardayda',
        'with_results' => 'le natiijooyin',
        'average_marks' => 'Dhibcaha Dhexdhexaadka',
        'pass_rate' => 'heerka guusha',
        'days_remaining' => 'Maalmo Hara',
        'ends' => 'Dhamaadaa',
        'overview' => 'Dulmarka',
        'subjects_tab' => 'Maadoyiinka',
        'results' => 'Natiijooyinka',
        'analytics' => 'Jaantuska',
        'exam_overview' => 'Dulmarka Imtixaanka',
        'marks_entry' => 'Gelinta Dhibcaha',
        'marks_description' => 'Geli oo maamul dhibcaha ardayda imtixaankan.',
        'manage_subjects' => 'Maamul Maadoyiinka',
        'subjects_description' => 'Ku dar ama ka saar maadoyiinka imtixaankan.',
        'exam_timeline' => 'Jadwalka Imtixaanka',
        'exam_created' => 'Imtixaanku waa la Aburay',
        'exam_scheduled' => 'Imtixaanku waa la abuuray oo la qorsheeyay',
        'subjects_added' => 'Maadoyiinka lagu daray',
        'subjects_configured' => 'maadoyiinka la diyaariyay',
        'marks_entry_progress' => 'Gelinta Dhibcaha',
        'results_entered' => 'natiijooyinka la geliyay ilaa hadda',
        'results_publication' => 'Soo bandhigida Natiijooyinka',
        'scheduled_completion' => 'Qorsheysan ka dib dhamaadka imtixaanka',
        'quick_stats' => 'Tirakoob Dhakhso leh',
        'passed' => 'Guulaystay',
        'failed' => 'Fashilmay',
        'absent' => 'Maqan',
        'present' => 'Jooga',
        'exam_subjects' => 'Maadoyiinka Imtixaanka',
        'max_marks' => 'Dhibcaha ugu Badan',
        'pass_marks' => 'Dhibcaha Guusha',
        'subject_summary' => 'Koobid Maadoyiinka',
        'total_subjects' => 'Wadarta Maadoyiinka',
        'total_max_marks' => 'Wadarta Dhibcaha ugu Badan',
        'total_pass_marks' => 'Wadarta Dhibcaha Guusha',
        'no_subjects_added' => 'Maado lama Darin',
        'no_subjects_message' => 'Ku dar maado si aad u bilowdo gelinta dhibcaha imtixaankan',
        'student_results' => 'Natiijooyinka Ardayda',
        'export' => 'U Dhoof',
        'print' => 'Daabac',
        'student' => 'Ardayga',
        'marks_obtained' => 'Dhibcaha La Helay',
        'status' => 'Xaalada',
        'grade' => 'Darajo',
        'remarks' => 'Faallooyin',
        'actions' => 'Ficillo',
        'no_remarks' => 'Faallooyin ma jiraan',
        'view_details' => 'Faahfaahin Arag',
        'edit_result' => 'Wax ka beddel Natiijada',
        'delete' => 'Tirtir',
        'total_results' => 'Wadarta Natiijooyinka',
        'students_passed' => 'Ardayda Guusha',
        'students_failed' => 'Ardayda Fashilay',
        'no_results_entered' => 'Natiijooyin Lama Geliyin',
        'no_results_message' => 'Geli dhibcaha ardayda si aad u aragto natiijooyinka imtixaankan',
        'enter_marks' => 'Gali Dhibcaha',
        'exam_analytics' => 'Jaantuska Imtixaanka',
        'attendance_overview' => 'Dulmarka Xuduurka',
        'top_performers' => 'Kuwa Ugu Fiican',
        'subject_performance' => 'Waxqabadka Qaybta',
        'average_marks_label' => 'Dhibcaha Dhexdhexaadka',
        'section' => 'Qaybta',
        'complete' => 'Dhammaystiran',
        'active' => 'Firfircoon',
        'completed' => 'Dhammaystiran',
        'upcoming' => 'Soo socda',
        'invalid_exam' => 'ID-ga imtixaanka aan sax ahayn.',
        'exam_not_found' => 'Imtixaan lama helin ama la diiday in la helo.',
        'complete_lower' => 'dhammaystiran',
        'exporting_results' => 'Natiijooyinka la soo dhoofayo...',
        'no_data_available' => 'Xog ma jiro',
        'showing' => 'Muujinaya',
        'to' => 'ilaa',
        'of' => 'ka',
        'entries' => 'galitaanka',
        'filtered_from' => 'laga soo soocay',
        'total_entries' => 'wadarta guud',
        'no_matching_records' => 'lama helin',
        'first' => 'Koowaad',
        'last' => 'Dambe',
        'next' => 'Xiga',
        'previous' => 'Hore',
        'search' => 'Raadi'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

/* ================= EXAM + OWNERSHIP ================= */
$stmt = $conn->prepare("
    SELECT 
        e.id, e.name, e.exam_type, e.class_id, e.section_id,
        e.start_date, e.end_date,
        c.name AS class_name
    FROM exams e
    JOIN classes c ON c.id = e.class_id
    JOIN class_subjects cs ON cs.class_id = e.class_id
    WHERE e.id = ? AND cs.teacher_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $exam_id, $teacher_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

if (!$exam) {
    die(t('exam_not_found'));
}

/* ================= EXAM SUBJECTS ================= */
$stmt = $conn->prepare("
    SELECT 
        s.id,
        s.name,
        es.max_marks,
        es.pass_marks
    FROM exam_subjects es
    JOIN subjects s ON s.id = es.subject_id
    WHERE es.exam_id = ?
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ================= STUDENT RESULTS ================= */
$stmt = $conn->prepare("
    SELECT 
        er.student_id,
        st.name AS student_name,
        er.subject_id,
        er.obtained_marks,
        er.status,
        er.remarks
    FROM exam_results er
    JOIN students st ON st.id = er.student_id
    JOIN exam_subjects es ON es.subject_id = er.subject_id AND es.exam_id = er.exam_id
    WHERE er.exam_id = ?
    ORDER BY st.name
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ================= INITIALIZE STATS ================= */
$subject_stats = [];
$students_set  = [];

$total_passed = 0;
$total_failed = 0;
$total_absent = 0;

foreach ($subjects as $s) {
    $subject_stats[$s['id']] = [
        'name'           => $s['name'],
        'max_marks'      => (float)$s['max_marks'],
        'pass_marks'     => (float)$s['pass_marks'],
        'total_marks'    => 0,
        'attempted'      => 0,
        'pass_count'     => 0,
        'fail_count'     => 0,
        'absent_count'   => 0,
        'avg_marks'      => 0
    ];
}


/* ================= PROCESS RESULTS ================= */
foreach ($results as $r) {

    $students_set[$r['student_id']] = true;
    $sid = (int)$r['subject_id'];

    if (!isset($subject_stats[$sid])) continue;

    if ($r['status'] === 'absent') {
        $subject_stats[$sid]['absent_count']++;
        $total_absent++;
        continue;
    }

    $marks = (float)$r['obtained_marks'];
    $subject_stats[$sid]['attempted']++;
    $subject_stats[$sid]['total_marks'] += $marks;

    if ($marks >= $subject_stats[$sid]['pass_marks']) {
        $subject_stats[$sid]['pass_count']++;
        $total_passed++;
    } else {
        $subject_stats[$sid]['fail_count']++;
        $total_failed++;
    }
}

/* ================= AVERAGES ================= */
foreach ($subject_stats as &$stat) {
    if ($stat['attempted'] > 0) {
        $stat['avg_marks'] = round(
            $stat['total_marks'] / $stat['attempted'], 2
        );
    }
}
unset($stat);

/* ================= TOTAL STUDENTS ================= */
$total_students = count($students_set);

/* ================= EXAM STATUS ================= */
$today = date('Y-m-d');

if ($today < $exam['start_date']) {
    $exam_status = t('upcoming');
} elseif ($today > $exam['end_date']) {
    $exam_status = t('completed');
} else {
    $exam_status = t('active');
}

/* ================= COMPLETION ================= */
$total_subjects = count($subjects);
$total_expected_results = $total_students * $total_subjects;
$total_entered_results  = count($results);

$completion_percent = $total_expected_results > 0
    ? round(($total_entered_results / $total_expected_results) * 100, 2)
    : 0;

/* ================= ESCAPE HELPER ================= */
function e($str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($exam['name']) ?> - <?= t('page_title') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.css">
    
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- AOS Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Exam Header -->
        <div class="exam-header" data-aos="fade-down">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <a href="exams.php" class="btn btn-light btn-sm me-3">
                            <i class="fas fa-arrow-left me-2"></i><?= t('back') ?>
                        </a>
                        <h1 class="h2 mb-0">
                            <i class="fas fa-file-alt me-3"></i><?= e($exam['name']) ?>
                        </h1>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <span class="exam-tag">
                            <i class="fas fa-graduation-cap"></i>
                            <?= e($exam['class_name']) ?>
                        </span>
                        <span class="exam-tag">
                            <i class="fas fa-layer-group"></i>
                            <?= t('section') ?> <?= e($exam['section_id']) ?>
                        </span>
                        <span class="exam-tag">
                            <i class="fas fa-tag"></i>
                            <?= e($exam['exam_type']) ?>
                        </span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="date-display">
                                <i class="fas fa-calendar-start me-2"></i>
                                <strong><?= t('start_date') ?>:</strong> 
                                <?= date('F j, Y', strtotime($exam['start_date'])) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="date-display">
                                <i class="fas fa-calendar-check me-2"></i>
                                <strong><?= t('end_date') ?>:</strong> 
                                <?= date('F j, Y', strtotime($exam['end_date'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 text-md-end">
                    <div class="progress-ring">
                        <canvas id="completionChart" width="120" height="120"></canvas>
                    </div>
                    <h4 id="completionText" class="mb-0">0% <?= t('complete') ?></h4>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions" data-aos="fade-up">
            <div class="d-flex flex-wrap">
                <a href="edit_exam.php?id=<?= $exam['id'] ?>" class="btn btn-primary quick-action-btn me-3 mb-2">
                    <i class="fas fa-edit me-2"></i><?= t('enter_edit_marks') ?>
                </a>
                <a href="add_exam_subjects.php?exam_id=<?= $exam['id'] ?>" class="btn btn-success quick-action-btn me-3 mb-2">
                    <i class="fas fa-plus me-2"></i><?= t('add_subjects') ?>
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-number" id="subjectCount"><?= count($subjects) ?></div>
                    <div class="stat-label"><?= t('subjects') ?></div>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        <span id="subjectsConfigured"><?= count($subjects) ?></span> <?= t('configured') ?>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-number" id="studentCount">0</div>
                    <div class="stat-label"><?= t('students') ?></div>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-user-check text-success me-1"></i>
                        <span id="studentsWithResults">0</span> <?= t('with_results') ?>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-number" id="avgMarks">0</div>
                    <div class="stat-label"><?= t('average_marks') ?></div>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-chart-line text-info me-1"></i>
                        <span id="passRate">0%</span> <?= t('pass_rate') ?>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-number" id="daysRemaining">0</div>
                    <div class="stat-label"><?= t('days_remaining') ?></div>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-clock me-1"></i>
                        <?= t('ends') ?> <?= date('M j', strtotime($exam['end_date'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" id="examTabs" role="tablist" data-aos="fade-up">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview">
                    <i class="fas fa-home me-2"></i><?= t('overview') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects">
                    <i class="fas fa-book me-2"></i><?= t('subjects_tab') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results">
                    <i class="fas fa-users me-2"></i><?= t('results') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics">
                    <i class="fas fa-chart-bar me-2"></i><?= t('analytics') ?>
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="examTabsContent" data-aos="fade-up" data-aos-delay="100">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i><?= t('exam_overview') ?></h4>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="action-card">
                                    <div class="action-icon" style="background-color: rgba(40, 167, 69, 0.1); color: var(--success-color);">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <h5><?= t('marks_entry') ?></h5>
                                    <p class="text-muted"><?= t('marks_description') ?></p>
                                    <a href="edit_exam.php?id=<?= $exam['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i><?= t('enter_marks') ?>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="action-card">
                                    <div class="action-icon" style="background-color: rgba(255, 193, 7, 0.1); color: var(--warning-color);">
                                        <i class="fas fa-book-medical"></i>
                                    </div>
                                    <h5><?= t('manage_subjects') ?></h5>
                                    <p class="text-muted"><?= t('subjects_description') ?></p>
                                    <a href="add_exam_subjects.php?exam_id=<?= $exam['id'] ?>" class="btn btn-warning">
                                        <i class="fas fa-cog me-2"></i><?= t('manage_subjects') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timeline -->
                        <h5 class="mb-3"><i class="fas fa-stream me-2"></i><?= t('exam_timeline') ?></h5>
                        <div class="timeline">
                            <div class="timeline-item">
                                <h6><?= t('exam_created') ?></h6>
                                <p class="text-muted small"><?= t('exam_scheduled') ?></p>
                            </div>
                            <div class="timeline-item">
                                <h6><?= t('subjects_added') ?></h6>
                                <p class="text-muted small"><?= count($subjects) ?> <?= t('subjects_configured') ?></p>
                            </div>
                            <div class="timeline-item">
                                <h6><?= t('marks_entry_progress') ?></h6>
                                <p class="text-muted small">
                                    <span id="resultsCount"><?= count($results) ?></span> <?= t('results_entered') ?>
                                </p>
                            </div>
                            <div class="timeline-item">
                                <h6><?= t('results_publication') ?></h6>
                                <p class="text-muted small"><?= t('scheduled_completion') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="chart-container">
                            <h5 class="mb-4"><i class="fas fa-chart-pie me-2"></i><?= t('quick_stats') ?></h5>
                            <canvas id="performanceChart" height="250"></canvas>
                            <div class="text-center mt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="fw-bold text-success" id="passedCount">0</div>
                                        <div class="small text-muted"><?= t('passed') ?></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-danger" id="failedCount">0</div>
                                        <div class="small text-muted"><?= t('failed') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Subjects Tab -->
            <div class="tab-pane fade" id="subjects" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-book me-2"></i><?= t('exam_subjects') ?></h4>
                    <span class="badge bg-primary">
                        <?= count($subjects) ?> <?= t('subjects') ?>
                    </span>
                </div>
                
                <?php if($subjects): ?>
                    <div class="row">
                        <?php 
                        $total_max = 0;
                        $total_pass = 0;
                        foreach($subjects as $sub): 
                            $total_max += $sub['max_marks'];
                            $total_pass += $sub['pass_marks'];
                        ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="action-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0"><?= e($sub['name']) ?></h5>
                                    <span class="badge bg-light text-dark">ID: <?= $sub['id'] ?></span>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted"><?= t('max_marks') ?></span>
                                        <span class="fw-bold"><?= $sub['max_marks'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted"><?= t('pass_marks') ?></span>
                                        <span class="fw-bold text-success"><?= $sub['pass_marks'] ?></span>
                                    </div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: <?= ($sub['pass_marks'] / $sub['max_marks']) * 100 ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Summary -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-calculator me-2"></i><?= t('subject_summary') ?></h5>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="fs-4 fw-bold"><?= count($subjects) ?></div>
                                    <div class="text-muted"><?= t('total_subjects') ?></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="fs-4 fw-bold"><?= $total_max ?></div>
                                    <div class="text-muted"><?= t('total_max_marks') ?></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="fs-4 fw-bold"><?= $total_pass ?></div>
                                    <div class="text-muted"><?= t('total_pass_marks') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-book-open"></i>
                        <h4 class="mb-3"><?= t('no_subjects_added') ?></h4>
                        <p class="mb-4"><?= t('no_subjects_message') ?></p>
                        <a href="add_exam_subjects.php?exam_id=<?= $exam['id'] ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i><?= t('add_subjects') ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Results Tab -->
            <div class="tab-pane fade" id="results" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-users me-2"></i><?= t('student_results') ?></h4>
                </div>
                
                <?php if($results): ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="resultsTable">
                            <thead class="table-light">
                                <tr>
                                    <th><?= t('student') ?></th>
                                    <th><?= t('marks_obtained') ?></th>
                                    <th><?= t('status') ?></th>
                                    <th><?= t('grade') ?></th>
                                    <th><?= t('remarks') ?></th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $passed = 0;
                                $failed = 0;
                                foreach($results as $res): 
                                    $grade = 'N/A';
                                    if (is_numeric($res['obtained_marks'])) {
                                        $percentage = ($res['obtained_marks'] / 100) * 100;
                                        if ($percentage >= 80) $grade = 'A';
                                        elseif ($percentage >= 70) $grade = 'B';
                                        elseif ($percentage >= 60) $grade = 'C';
                                        elseif ($percentage >= 50) $grade = 'D';
                                        else $grade = 'F';
                                    }
                                    
                                    if ($grade === 'F') {
                                        $failed++;
                                    } elseif ($grade !== 'N/A') {
                                        $passed++;
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium"><?= e($res['student_name']) ?></div>
                                        <div class="small text-muted">ID: <?= substr(md5($res['student_name']), 0, 8) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= $res['obtained_marks'] ?? 'N/A' ?></div>
                                    </td>
                                    <td>
                                        <span class="result-status status-<?= strtolower($res['status']) ?>">
                                            <?= e($res['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($grade !== 'N/A'): ?>
                                            <span class="grade-badge grade-<?= $grade ?>">
                                                <?= $grade ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($res['remarks']): ?>
                                            <span class="small"><?= e($res['remarks']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small"><?= t('no_remarks') ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Results Summary -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="fs-3 fw-bold text-primary"><?= count($results) ?></div>
                                    <div class="text-muted"><?= t('total_results') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="fs-3 fw-bold text-success"><?= $passed ?></div>
                                    <div class="text-muted"><?= t('students_passed') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="fs-3 fw-bold text-danger"><?= $failed ?></div>
                                    <div class="text-muted"><?= t('students_failed') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-user-graduate"></i>
                        <h4 class="mb-3"><?= t('no_results_entered') ?></h4>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Analytics Tab -->
            <div class="tab-pane fade" id="analytics" role="tabpanel">
                <h4 class="mb-4"><i class="fas fa-chart-bar me-2"></i><?= t('exam_analytics') ?></h4>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="chart-container">
                            <h5 class="mb-4"><?= t('attendance_overview') ?></h5>
                            <canvas id="attendanceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-trophy me-2"></i><?= t('top_performers') ?></h5>
                                <div class="list-group" id="topPerformers">
                                    <div class="list-group-item text-center text-muted">
                                        <?= t('no_data_available') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-chart-line me-2"></i><?= t('subject_performance') ?></h5>
                                <canvas id="subjectPerformanceChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script>
        /* ================= INIT ================= */
        AOS.init({ duration: 800, once: true });

        toastr.options = {
            positionClass: "toast-top-right",
            closeButton: true,
            progressBar: true,
            timeOut: 4000
        };

        $(document).ready(function () {
            /* ================= DATATABLE ================= */
            $('#resultsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[1, 'desc']],
                language: {
                    search: "<?= t('search') ?>:",
                    lengthMenu: "<?= t('show') ?> _MENU_ <?= t('entries') ?>",
                    info: "<?= t('showing') ?> _START_ <?= t('to') ?> _END_ <?= t('of') ?> _TOTAL_ <?= t('entries') ?>",
                    infoEmpty: "<?= t('showing') ?> 0 <?= t('to') ?> 0 <?= t('of') ?> 0 <?= t('entries') ?>",
                    infoFiltered: "(<?= t('filtered_from') ?> _MAX_ <?= t('total_entries') ?>)",
                    zeroRecords: "<?= t('no_matching_records') ?>",
                    paginate: {
                        first: "<?= t('first') ?>",
                        last: "<?= t('last') ?>",
                        next: "<?= t('next') ?>",
                        previous: "<?= t('previous') ?>"
                    }
                }
            });

            /* ================= PHP → JS DATA ================= */
            const examData = {
                subjects: <?= json_encode($subject_stats) ?>,
                totalStudents: <?= (int)$total_students ?>,
                passed: <?= (int)$total_passed ?>,
                failed: <?= (int)$total_failed ?>,
                absent: <?= (int)$total_absent ?>,
                completion: <?= (float)$completion_percent ?>,
                endDate: "<?= $exam['end_date'] ?>"
            };

            /* ================= STATS ================= */
            function updateStatistics() {
                const today = new Date();
                const endDate = new Date(examData.endDate);
                const daysRemaining = Math.max(
                    Math.ceil((endDate - today) / (1000 * 60 * 60 * 24)), 0
                );

                $('#subjectCount').text(Object.keys(examData.subjects).length);
                $('#daysRemaining').text(daysRemaining);
                $('#resultsCount').text(examData.totalStudents);

                $('#completionText').text(examData.completion + '% <?= t('complete') ?>');

                const attempted = examData.passed + examData.failed;
                const avgMarks = attempted > 0
                    ? (
                        Object.values(examData.subjects)
                            .reduce((t, s) => t + s.total_marks, 0) /
                        attempted
                    ).toFixed(1)
                    : 0;

                const passRate = attempted > 0
                    ? Math.round((examData.passed / attempted) * 100)
                    : 0;

                $('#avgMarks').text(avgMarks);
                $('#passRate').text(passRate + '%');
                $('#passedCount').text(examData.passed);
                $('#failedCount').text(examData.failed);

                updateProgressChart(examData.completion);
                updatePerformanceChart(
                    examData.passed,
                    examData.failed,
                    examData.absent
                );
            }

            /* ================= COMPLETION CHART ================= */
            let completionChart;
            function updateProgressChart(percent) {
                const ctx = document.getElementById('completionChart').getContext('2d');
                if (completionChart) completionChart.destroy();

                completionChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [percent, 100 - percent],
                            backgroundColor: ['#5d78ff', '#eef2ff'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        cutout: '85%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });
            }

            /* ================= PASS / FAIL ================= */
            let performanceChart;
            function updatePerformanceChart(passed, failed, absent) {
                const ctx = document.getElementById('performanceChart').getContext('2d');
                if (performanceChart) performanceChart.destroy();

                performanceChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['<?= t('passed') ?>', '<?= t('failed') ?>', '<?= t('absent') ?>'],
                        datasets: [{
                            data: [passed, failed, absent],
                            backgroundColor: ['#28a745', '#dc3545', '#6c757d']
                        }]
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }

            /* ================= ANALYTICS ================= */
            function initializeAnalyticsCharts() {
                /* SUBJECT AVERAGES */
                const subjectLabels = [];
                const subjectAverages = [];

                Object.values(examData.subjects).forEach(s => {
                    subjectLabels.push(s.name);
                    subjectAverages.push(s.avg_marks);
                });

                new Chart(
                    document.getElementById('subjectPerformanceChart'),
                    {
                        type: 'line',
                        data: {
                            labels: subjectLabels,
                            datasets: [{
                                label: '<?= t('average_marks_label') ?>',
                                data: subjectAverages,
                                borderColor: '#5d78ff',
                                backgroundColor: 'rgba(93,120,255,.1)',
                                fill: true
                            }]
                        },
                        options: { plugins: { legend: { display: false } } }
                    }
                );

                /* ATTENDANCE */
                new Chart(
                    document.getElementById('attendanceChart'),
                    {
                        type: 'doughnut',
                        data: {
                            labels: ['<?= t('present') ?>', '<?= t('absent') ?>'],
                            datasets: [{
                                data: [
                                    examData.totalStudents - examData.absent,
                                    examData.absent
                                ],
                                backgroundColor: ['#28a745', '#dc3545']
                            }]
                        },
                        options: { plugins: { legend: { position: 'bottom' } } }
                    }
                );
            }

            /* ================= TOP PERFORMERS ================= */
            function updateTopPerformers() {
                const performers = [];

                Object.values(examData.subjects).forEach(s => {
                    performers.push({
                        name: s.name,
                        avg: s.avg_marks
                    });
                });

                performers.sort((a, b) => b.avg - a.avg);

                const list = $('#topPerformers').empty();
                if (performers.length === 0) {
                    list.append(`
                        <div class="list-group-item text-center text-muted">
                            <?= t('no_data_available') ?>
                        </div>
                    `);
                } else {
                    performers.slice(0, 5).forEach((p, i) => {
                        list.append(`
                            <div class="list-group-item d-flex justify-content-between">
                                <span><span class="badge bg-primary me-2">${i + 1}</span>${p.name}</span>
                                <strong>${p.avg}</strong>
                            </div>
                        `);
                    });
                }
            }

            /* ================= EVENTS ================= */
            $('#analytics-tab').on('shown.bs.tab', function() {
                initializeAnalyticsCharts();
                updateTopPerformers();
            });

            /* ================= KEYBOARD ================= */
            $(document).on('keydown', e => {
                if (e.ctrlKey) {
                    const map = {
                        '1': '#overview-tab',
                        '2': '#subjects-tab',
                        '3': '#results-tab',
                        '4': '#analytics-tab'
                    };
                    if (map[e.key]) {
                        e.preventDefault();
                        $(map[e.key]).tab('show');
                    }
                }
            });

            // Initial setup
            updateStatistics();
        });
    </script>
</body>
</html>

<style>
        :root {
            --primary-color: #5d78ff;
            --primary-light: #eef2ff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --border-radius: 12px;
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #f0f2ff 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            padding-bottom: 50px;
        }
        
        .exam-header {
            background: linear-gradient(135deg, var(--primary-color), #7c8eff);
            color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .exam-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .exam-header::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .header-actions {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }
        
        .action-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: none;
            height: 100%;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1;
            color: var(--primary-color);
        }
        
        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .subject-badge {
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            transition: var(--transition);
        }
        
        .subject-badge:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.05);
        }
        
        .result-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-present { background: #d4edda; color: #155724; }
        .status-absent { background: #f8d7da; color: #721c24; }
        
        .grade-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        .grade-A { background: linear-gradient(135deg, #00c853, #64dd17); color: white; }
        .grade-B { background: linear-gradient(135deg, #33b5e5, #0099cc); color: white; }
        .grade-C { background: linear-gradient(135deg, #ffbb33, #ff8800); color: white; }
        .grade-D { background: linear-gradient(135deg, #ff4444, #cc0000); color: white; }
        .grade-F { background: linear-gradient(135deg, #880e4f, #560027); color: white; }
        
        .progress-ring {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
            margin-bottom: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-light);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -33px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(93, 120, 255, 0.05);
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: var(--secondary-color);
        }
        
        .no-data i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #e9ecef;
        }
        
        .quick-actions {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
        }
        
        .quick-action-btn {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 500;
            transition: var(--transition);
            border: 2px solid transparent;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-3px);
            border-color: var(--primary-color);
        }
        
        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--card-shadow);
            height: 100%;
        }
        
        .exam-tag {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .date-display {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 10px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            font-weight: 500;
            padding: 12px 25px;
            border-radius: 8px 8px 0 0;
            transition: var(--transition);
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: white;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .tab-content {
            background: white;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            padding: 30px;
            box-shadow: var(--card-shadow);
        }
        
        @media (max-width: 768px) {
            .exam-header {
                padding: 20px;
            }
            
            .stat-number {
                font-size: 1.8rem;
            }
            
            .quick-action-btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>