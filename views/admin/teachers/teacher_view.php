<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Teacher Profile | School System',
        'back_to_teachers' => 'Back to Teachers',
        'teacher_profile' => 'Teacher Profile',
        'comprehensive_overview' => 'Comprehensive overview and analytics',
         
        'staff_member' => 'Staff Member',
        'since' => 'Since',
        'subjects' => 'Subjects',
        'classes' => 'Classes',
        'total_hours' => 'Total Hours',
        'experience' => 'Experience',
        'year' => 'year',
        'years' => 'years',
        'personal_information' => 'Personal Information',
        'basic_details' => 'Basic details and contact information',
        'employee_code' => 'Employee Code',
        'gender' => 'Gender',
        'contact_details' => 'Contact Details',
        'professional_information' => 'Professional Information',
        'career_details' => 'Career details and qualifications',
        'qualification' => 'Qualification',
        'hire_date' => 'Hire Date',
        'member_since' => 'Member Since',
        'account_status' => 'Account Status',
        'teaching_assignments' => 'Teaching Assignments',
        'current_classes' => 'Current classes and subjects',
        'total_assignments' => 'Total Assignments',
        'no_assignments_found' => 'No Assignments Found',
        'no_assignments_message' => 'This teacher is not currently assigned to any classes or subjects.',
        'table_section' => 'Section',
        'table_class' => 'Class',
        'table_subject' => 'Subject',
        'table_credit_hours' => 'Credit Hours',
        'table_details' => 'Details',
        'code' => 'Code',
        'view_details' => 'View Details',
        'total_credit_hours' => 'Total Credit Hours',
        'unique_sections' => 'Unique Sections',
        'workload_density' => 'Workload Density',
        'edit_profile' => 'Edit Profile',
        'current_status' => 'Current Status',
        'last_updated' => 'Last Updated',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'male' => 'Male',
        'female' => 'Female',
        'ago' => 'ago',
        'invalid_teacher_id' => 'Invalid teacher ID',
        'teacher_not_found' => 'Teacher not found'
    ],
    2 => [ // Arabic - Simple language for basic understanding
        'page_title' => 'ملف المعلم | النظام المدرسي',
        'back_to_teachers' => 'العودة للمعلمين',
        'teacher_profile' => 'ملف المعلم',
        'comprehensive_overview' => 'نظرة شاملة وتحليلات',
         
        'staff_member' => 'عضو هيئة التدريس',
        'since' => 'منذ',
        'subjects' => 'المواد',
        'classes' => 'الصفوف',
        'total_hours' => 'إجمالي الساعات',
        'experience' => 'الخبرة',
        'year' => 'سنة',
        'years' => 'سنوات',
        'personal_information' => 'المعلومات الشخصية',
        'basic_details' => 'تفاصيل أساسية ومعلومات الاتصال',
        'employee_code' => 'رمز الموظف',
        'gender' => 'الجنس',
        'contact_details' => 'تفاصيل الاتصال',
        'professional_information' => 'المعلومات المهنية',
        'career_details' => 'تفاصيل المسيرة المهنية والمؤهلات',
        'qualification' => 'المؤهل',
        'hire_date' => 'تاريخ التعيين',
        'member_since' => 'عضو منذ',
        'account_status' => 'حالة الحساب',
        'teaching_assignments' => 'المهام التعليمية',
        'current_classes' => 'الصفوف والمواد الحالية',
        'total_assignments' => 'إجمالي المهام',
        'no_assignments_found' => 'لم يتم العثور على مهام',
        'no_assignments_message' => 'هذا المعلم غير معين حاليًا في أي صفوف أو مواد.',
        'table_section' => 'القسم',
        'table_class' => 'الصف',
        'table_subject' => 'المادة',
        'table_credit_hours' => 'الساعات المعتمدة',
        'table_details' => 'تفاصيل',
        'code' => 'الرمز',
        'view_details' => 'عرض التفاصيل',
        'total_credit_hours' => 'إجمالي الساعات المعتمدة',
        'unique_sections' => 'الأقسام الفريدة',
        'workload_density' => 'كثافة العمل',
        'edit_profile' => 'تعديل الملف',
        'current_status' => 'الحالة الحالية',
        'last_updated' => 'آخر تحديث',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'suspended' => 'معلق',
        'male' => 'ذكر',
        'female' => 'أنثى',
        'ago' => 'منذ',
        'invalid_teacher_id' => 'معرف المعلم غير صالح',
        'teacher_not_found' => 'المعلم غير موجود'
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

$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) { header('Location: ../../auth/login.php'); exit; }
function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die(t('invalid_teacher_id'));
}
$id = (int)$_GET['id'];

/* ================= TEACHER BASIC DETAILS ================= */
$teacherStmt = $conn->prepare("SELECT * FROM teachers WHERE id=? LIMIT 1");
$teacherStmt->bind_param("i", $id);
$teacherStmt->execute();
$teacherRes = $teacherStmt->get_result();

if($teacherRes->num_rows == 0){
    die(t('teacher_not_found'));
}
$teacher = $teacherRes->fetch_assoc();

/* ================= ASSIGNED CLASSES & SUBJECTS ================= */
$assignments = [];
$assignStmt = $conn->prepare("
    SELECT 
        s.name AS subject,
        s.code AS subject_code,
        c.name AS class,
        sec.name AS section,
        css.day_of_week,
        css.start_time,
        css.end_time
    FROM class_subjects cs
    JOIN subjects s ON s.id = cs.subject_id
    JOIN classes c ON c.id = cs.class_id
    LEFT JOIN sections sec ON sec.id = c.section_id
    LEFT JOIN class_subject_schedule css ON cs.id = css.class_subject_id
    WHERE cs.teacher_id=?
    ORDER BY sec.name, c.name, css.day_of_week, css.start_time
");
$assignStmt->bind_param("i", $id);
$assignStmt->execute();
$assignRes = $assignStmt->get_result();

while($row = $assignRes->fetch_assoc()){
    $assignments[] = $row;
}

/* ================= WORKLOAD STATISTICS ================= */
$statStmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT cs.subject_id) AS total_subjects,
        COUNT(DISTINCT cs.class_id) AS total_classes,
        SUM(s.credit_hours) AS total_hours
    FROM class_subjects cs
    JOIN subjects s ON s.id = cs.subject_id
    WHERE cs.teacher_id=?
");
$statStmt->bind_param("i", $id);
$statStmt->execute();
$statRes = $statStmt->get_result()->fetch_assoc();

// Calculate experience
$hireDate = new DateTime($teacher['hire_date']);
$today = new DateTime();
$experience = $today->diff($hireDate)->y;
$experienceText = $experience . ' ' . ($experience !== 1 ? t('years') : t('year'));
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> | <?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #7c3aed;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .avatar-glow {
            position: relative;
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.3);
        }

        .avatar-glow::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: inherit;
            z-index: -1;
            opacity: 0.5;
        }

        .table-glow tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background-color 0.2s ease;
        }

        .table-glow tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .scrollbar-custom::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .scrollbar-custom::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            gap: 6px;
        }

        .status-active {
            background: rgba(5, 150, 105, 0.2);
            color: #10b981;
            border: 1px solid rgba(5, 150, 105, 0.3);
        }

        .status-inactive {
            background: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
            border: 1px solid rgba(100, 116, 139, 0.3);
        }

        .status-suspended {
            background: rgba(220, 38, 38, 0.2);
            color: #f87171;
            border: 1px solid rgba(220, 38, 38, 0.3);
        }

        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
            
            .glass-card {
                background: white !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
            }
        }

        @media (max-width: 768px) {
            .container-padding {
                padding: 1rem !important;
            }
            
            .avatar-glow {
                width: 80px !important;
                height: 80px !important;
                font-size: 24px !important;
            }
            
            .text-responsive {
                font-size: 1.5rem !important;
            }
            
            .grid-responsive {
                grid-template-columns: 1fr !important;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in-delay-1 { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }
        .fade-in-delay-3 { animation-delay: 0.3s; }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Main Container -->
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header with Navigation -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
            <div class="flex items-center gap-4">
                <a href="teachers.php" 
                   class="inline-flex items-center gap-3 px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 hover:shadow-lg no-print">
                    <i class="fas fa-arrow-left"></i>
                    <span><?= t('back_to_teachers') ?></span>
                </a>
                <div class="hidden md:block w-px h-12 bg-gradient-to-b from-transparent via-white/20 to-transparent"></div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white"><?= t('teacher_profile') ?></h1>
                    <p class="text-gray-400 text-sm mt-1"><?= t('comprehensive_overview') ?></p>
                </div>
            </div>
            
            
        </div>

        <!-- Teacher Profile Header -->
        <div class="glass-card p-6 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <!-- Avatar -->
                <div class="relative">
                    <div class="avatar-glow w-24 h-24 rounded-2xl flex items-center justify-center text-3xl font-bold bg-gradient-to-br from-blue-600 to-purple-600 text-white">
                        <?= strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)) ?>
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        ID: <?= $teacher['id'] ?>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div class="mb-4 md:mb-0">
                            <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">
                                <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                            </h2>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-gray-400 text-sm">
                                    <i class="fas fa-id-card mr-1.5"></i><?= $teacher['employee_code'] ?>
                                </span>
                                <?php if($teacher['status'] == 'active'): ?>
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle"></i><?= t('active') ?>
                                    </span>
                                <?php elseif($teacher['status'] == 'inactive'): ?>
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause-circle"></i><?= t('inactive') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-suspended">
                                        <i class="fas fa-times-circle"></i><?= t('suspended') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-amber-400"><?= t('staff_member') ?></div>
                            <div class="text-gray-400 text-sm"><?= t('since') ?> <?= date('Y', strtotime($teacher['hire_date'])) ?></div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="glass-card p-4 text-center hover-lift">
                            <div class="text-gray-400 text-sm mb-2"><?= t('subjects') ?></div>
                            <div class="text-2xl font-bold text-gradient"><?= $statRes['total_subjects'] ?></div>
                        </div>
                        <div class="glass-card p-4 text-center hover-lift">
                            <div class="text-gray-400 text-sm mb-2"><?= t('classes') ?></div>
                            <div class="text-2xl font-bold text-gradient"><?= $statRes['total_classes'] ?></div>
                        </div>
                        <div class="glass-card p-4 text-center hover-lift">
                            <div class="text-gray-400 text-sm mb-2"><?= t('total_hours') ?></div>
                            <div class="text-2xl font-bold text-gradient"><?= $statRes['total_hours'] ?? 0 ?></div>
                        </div>
                        <div class="glass-card p-4 text-center hover-lift">
                            <div class="text-gray-400 text-sm mb-2"><?= t('experience') ?></div>
                            <div class="text-2xl font-bold text-gradient">
                                <?= $experienceText ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Personal Information -->
            <div class="glass-card p-6 hover-lift animate-fade-in fade-in-delay-1">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-3 rounded-xl mr-4">
                        <i class="fas fa-user-circle text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white"><?= t('personal_information') ?></h3>
                        <p class="text-gray-400 text-sm"><?= t('basic_details') ?></p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-gray-400 text-sm mb-1"><?= t('employee_code') ?></div>
                            <div class="font-semibold text-white"><?= $teacher['employee_code'] ?></div>
                        </div>
                        <div>
                            <div class="text-gray-400 text-sm mb-1"><?= t('gender') ?></div>
                            <div class="font-semibold text-white">
                                <?= $teacher['gender'] == 'male' ? t('male') : t('female') ?>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-gray-400 text-sm mb-3"><?= t('contact_details') ?></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                    <i class="fas fa-phone text-blue-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-400"><?= t('phone') ?></div>
                                    <div class="font-semibold text-white"><?= $teacher['phone'] ?></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                    <i class="fas fa-envelope text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-400"><?= t('email') ?></div>
                                    <div class="font-semibold text-white"><?= $teacher['email'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="glass-card p-6 hover-lift animate-fade-in fade-in-delay-2">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-3 rounded-xl mr-4">
                        <i class="fas fa-graduation-cap text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white"><?= t('professional_information') ?></h3>
                        <p class="text-gray-400 text-sm"><?= t('career_details') ?></p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <div class="text-gray-400 text-sm mb-1"><?= t('qualification') ?></div>
                        <div class="font-semibold text-lg text-white"><?= $teacher['qualification'] ?></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-gray-400 text-sm mb-1"><?= t('hire_date') ?></div>
                            <div class="font-semibold text-white">
                                <?= date('F j, Y', strtotime($teacher['hire_date'])) ?>
                                <div class="text-gray-400 text-xs mt-1">
                                    <?= round((time() - strtotime($teacher['hire_date'])) / (365 * 24 * 60 * 60), 1) ?> <?= t('years') ?> <?= t('ago') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-gray-400 text-sm mb-1"><?= t('member_since') ?></div>
                            <div class="font-semibold text-white">
                                <?= date('F Y', strtotime($teacher['created_at'])) ?>
                                <div class="text-gray-400 text-xs mt-1">
                                    <?= round((time() - strtotime($teacher['created_at'])) / (365 * 24 * 60 * 60), 1) ?> <?= t('years') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-400 text-sm mb-3"><?= t('account_status') ?></div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 bg-gray-700 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full 
                                    <?= $teacher['status'] == 'active' ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : '' ?>
                                    <?= $teacher['status'] == 'inactive' ? 'bg-gradient-to-r from-gray-500 to-gray-400' : '' ?>
                                    <?= $teacher['status'] == 'suspended' ? 'bg-gradient-to-r from-red-500 to-red-400' : '' ?>"
                                    style="width: <?= $teacher['status'] == 'active' ? '100' : ($teacher['status'] == 'inactive' ? '50' : '25') ?>%">
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-white uppercase"><?= $teacher['status'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments Section -->
        <div class="glass-card overflow-hidden hover-lift animate-fade-in fade-in-delay-3">
            <div class="gradient-bg px-6 py-4">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white"><?= t('teaching_assignments') ?></h3>
                            <p class="text-white/80 text-sm"><?= t('current_classes') ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-white"><?= count($assignments) ?></div>
                        <div class="text-white/80 text-sm"><?= t('total_assignments') ?></div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <?php if(count($assignments) == 0): ?>
                <div class="text-center py-12">
                    <div class="text-gray-400 text-5xl mb-4">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-300 mb-2"><?= t('no_assignments_found') ?></h4>
                    <p class="text-gray-400"><?= t('no_assignments_message') ?></p>
                </div>
                <?php else: ?>
                
                <div class="overflow-x-auto scrollbar-custom">
                    <table class="w-full min-w-full divide-y divide-gray-700/50">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-500/10 to-purple-500/10">
                                <th class="px-6 py-4 text-left font-semibold text-gray-300">
                                    <i class="fas fa-layer-group mr-2"></i><?= t('table_section') ?>
                                </th>
                                <th class="px-6 py-4 text-left font-semibold text-gray-300">
                                    <i class="fas fa-school mr-2"></i><?= t('table_class') ?>
                                </th>
                                <th class="px-6 py-4 text-left font-semibold text-gray-300">
                                    <i class="fas fa-book mr-2"></i><?= t('table_subject') ?>
                                </th>
                                <th class="px-6 py-4 text-left font-semibold text-gray-300">
                                    <i class="fas fa-clock mr-2"></i><?= t('table_credit_hours') ?>
                                </th>
                                <th class="px-6 py-4 text-left font-semibold text-gray-300">
                                    <i class="fas fa-info-circle mr-2"></i><?= t('table_details') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/30">
                            <?php foreach($assignments as $a): ?>
                            <tr class="hover:bg-white/5 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                            <i class="fas fa-layer-group text-blue-400"></i>
                                        </div>
                                        <span class="text-white"><?= $a['section'] ?? 'N/A' ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                            <i class="fas fa-school text-purple-400"></i>
                                        </div>
                                        <span class="font-medium text-white"><?= $a['class'] ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                                            <i class="fas fa-book text-emerald-400"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white"><?= htmlspecialchars($a['subject']) ?></div>
                                            <?php if(isset($a['subject_code'])): ?>
                                                <div class="text-gray-400 text-xs mt-1">
                                                    <?= t('code') ?>: <?= htmlspecialchars($a['subject_code']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($a['day_of_week'])): ?>
                                                <div class="text-gray-400 text-xs"><?= $a['day_of_week'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                                            <i class="fas fa-clock text-amber-400"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-lg text-white">
                                                <?= isset($a['start_time'], $a['end_time']) ? 
                                                    date('H:i', strtotime($a['start_time'])) . ' - ' . date('H:i', strtotime($a['end_time'])) : '-' ?>
                                            </div>
                                            <div class="text-gray-400 text-xs"><?= t('total_hours') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/10 rounded-lg text-white hover:bg-white/20 transition-all">
                                        <i class="fas fa-eye"></i>
                                        <span><?= t('view_details') ?></span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Assignment Summary -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="glass-card p-4 text-center hover-lift">
                        <div class="text-gray-400 text-sm mb-2"><?= t('total_credit_hours') ?></div>
                        <div class="text-2xl font-bold text-gradient">
                            <?= array_sum(array_column($assignments, 'credit_hours')) ?>
                        </div>
                    </div>
                    <div class="glass-card p-4 text-center hover-lift">
                        <div class="text-gray-400 text-sm mb-2"><?= t('unique_sections') ?></div>
                        <div class="text-2xl font-bold text-gradient">
                            <?= count(array_unique(array_column($assignments, 'section'))) ?>
                        </div>
                    </div>
                    <div class="glass-card p-4 text-center hover-lift">
                        <div class="text-gray-400 text-sm mb-2"><?= t('workload_density') ?></div>
                        <div class="text-2xl font-bold text-gradient">
                            <?= count($assignments) > 0 ? round(count($assignments) / $statRes['total_classes'], 2) : 0 ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Actions -->
        <?php if(!is_finance()): ?>
        <div class="mt-8 flex justify-center no-print">
            <a href="teacher_edit.php?id=<?= $id ?>" 
               class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 hover:shadow-lg">
                <i class="fas fa-edit"></i>
                <span><?= t('edit_profile') ?></span>
            </a>
        </div>
        <?php endif; ?>

        <!-- Status Indicators -->
        <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-20 no-print">
            <div class="glass-card p-3 rounded-xl text-center backdrop-blur-sm">
                <div class="text-xs text-gray-400 mb-1"><?= t('current_status') ?></div>
                <div class="font-semibold <?= $teacher['status'] == 'active' ? 'text-emerald-400' : ($teacher['status'] == 'inactive' ? 'text-gray-400' : 'text-red-400') ?>">
                    <?= strtoupper($teacher['status']) ?>
                </div>
            </div>
            <div class="glass-card p-3 rounded-xl text-center backdrop-blur-sm">
                <div class="text-xs text-gray-400 mb-1"><?= t('last_updated') ?></div>
                <div class="font-semibold text-sm text-white"><?= date('M j, Y') ?></div>
            </div>
        </div>
    </div>

    <script>
        function printProfile() {
            window.print();
        }

        function shareProfile() {
            if (navigator.share) {
                navigator.share({
                    title: 'Teacher Profile: <?= htmlspecialchars($teacher["first_name"] . " " . $teacher["last_name"]) ?>',
                    text: 'Check out this teacher profile from the school management system',
                    url: window.location.href
                });
            } else {
                alert('Share feature is available in modern browsers');
            }
        }

        function generateReport() {
            alert('Report generation feature would be implemented here');
        }

        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add intersection observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            // Observe all cards
            document.querySelectorAll('.glass-card').forEach(card => {
                observer.observe(card);
            });

            // Add hover effects
            const hoverElements = document.querySelectorAll('.hover-lift');
            hoverElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                
                element.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });

        // Handle responsive behavior
        function handleResponsive() {
            const screenWidth = window.innerWidth;
            const textElements = document.querySelectorAll('.text-responsive');
            
            textElements.forEach(el => {
                if (screenWidth < 768) {
                    el.classList.add('text-sm');
                } else {
                    el.classList.remove('text-sm');
                }
            });
        }

        window.addEventListener('resize', handleResponsive);
        window.addEventListener('load', handleResponsive);
    </script>
</body>
</html>