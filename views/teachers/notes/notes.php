<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
$message = "";

/* ===================== TRANSLATIONS ===================== */
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Class Notes Manager | Academic Portal',
        'dashboard_title' => 'Class Notes Manager',
        'dashboard_subtitle' => 'Organize and manage your teaching notes efficiently',
        'total_notes' => 'Total Notes',
        'classes' => 'Classes',
        'words_written' => 'Words Written',
        'last_updated' => 'Last Updated',
        'add_new_note' => 'Add New Note',
        'edit_note' => 'Edit Note',
        'update_note' => 'Update Note',
        'save_note' => 'Save Note',
        'cancel' => 'Cancel',
        'clear' => 'Clear',
        'class' => 'Class',
        'select_class' => 'Select Class',
        'class_instruction' => 'Choose the class for this note',
        'note' => 'Note',
        'note_placeholder' => 'Write your notes here...',
        'note_instruction' => 'You can include lesson plans, student observations, or important reminders',
        'my_notes' => 'My Notes',
        'all_notes' => 'All your notes sorted by creation date',
        'no_notes_found' => 'No Notes Found',
        'no_notes_message' => 'You haven\'t created any notes yet. Start by adding your first note above.',
        'read_more' => 'Read more',
        'read_less' => 'Read less',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this note?',
        'note_deleted' => 'Note deleted successfully.',
        'note_updated' => 'Note updated successfully.',
        'note_added' => 'Note added successfully.',
        'validation_error' => 'Please select a class and write a note.',
        'words' => 'words',
        'characters' => 'characters',
        'created' => 'Created',
        'updated' => 'Updated'
    ],
    2 => [ // Arabic
        'page_title' => 'مدير ملاحظات الفصل | البوابة الأكاديمية',
        'dashboard_title' => 'مدير ملاحظات الفصل',
        'dashboard_subtitle' => 'نظم وإدارة ملاحظات التدريس الخاصة بك بكفاءة',
        'total_notes' => 'إجمالي الملاحظات',
        'classes' => 'الفصول',
        'words_written' => 'الكلمات المكتوبة',
        'last_updated' => 'آخر تحديث',
        'add_new_note' => 'إضافة ملاحظة جديدة',
        'edit_note' => 'تعديل الملاحظة',
        'update_note' => 'تحديث الملاحظة',
        'save_note' => 'حفظ الملاحظة',
        'cancel' => 'إلغاء',
        'clear' => 'مسح',
        'class' => 'الفصل',
        'select_class' => 'اختر الفصل',
        'class_instruction' => 'اختر الفصل لهذه الملاحظة',
        'note' => 'الملاحظة',
        'note_placeholder' => 'اكتب ملاحظاتك هنا...',
        'note_instruction' => 'يمكنك تضمين خطط الدروس، ملاحظات الطلاب، أو التذكيرات المهمة',
        'my_notes' => 'ملاحظاتي',
        'all_notes' => 'جميع ملاحظاتك مرتبة حسب تاريخ الإنشاء',
        'no_notes_found' => 'لم يتم العثور على ملاحظات',
        'no_notes_message' => 'لم تقم بإنشاء أي ملاحظات بعد. ابدأ بإضافة ملاحظتك الأولى أعلاه.',
        'read_more' => 'قراءة المزيد',
        'read_less' => 'قراءة أقل',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'delete_confirm' => 'حذف هذه الملاحظة؟',
        'note_deleted' => 'تم حذف الملاحظة بنجاح.',
        'note_updated' => 'تم تحديث الملاحظة بنجاح.',
        'note_added' => 'تم إضافة الملاحظة بنجاح.',
        'validation_error' => 'الرجاء اختيار فصل وكتابة ملاحظة.',
        'words' => 'كلمات',
        'characters' => 'أحرف',
        'created' => 'تم الإنشاء',
        'updated' => 'تم التحديث'
    ],
    3 => [ // Somali
        'page_title' => 'Maareeyaha Note-yada Fasalka | Portal-ka Tacliinta',
        'dashboard_title' => 'Maareeyaha Note-yada Fasalka',
        'dashboard_subtitle' => 'Habee oo maamul note-yada waxbarashadaada si wax ku ool ah',
        'total_notes' => 'Wadarta Note-yada',
        'classes' => 'Fasallada',
        'words_written' => 'Erayada La Qoray',
        'last_updated' => 'Cusbooneysiinta Ugu Dambeysay',
        'add_new_note' => 'Ku Dar Note Cusub',
        'edit_note' => 'Wax ka beddel Note',
        'update_note' => 'Cusboonaysii Note',
        'save_note' => 'Keydso Note',
        'cancel' => 'Jooji',
        'clear' => 'Nadiif',
        'class' => 'Fasalka',
        'select_class' => 'Dooro Fasalka',
        'class_instruction' => 'Dooro fasalka note-kan',
        'note' => 'Note',
        'note_placeholder' => 'Ku qor note-yadaada halkan...',
        'note_instruction' => 'Waxaad ku darsan kartaa qorshayaasha casharka, ardayda la arko, ama xusuusin muhiim ah',
        'my_notes' => 'Note-yadayda',
        'all_notes' => 'Dhammaan note-yadaada oo kala saaran taariikhda abuurista',
        'no_notes_found' => 'Note Lama Helin',
        'no_notes_message' => 'Weli ma abuurid note. Bilaab adiga oo ku dara note-kaaga koowaad kor.',
        'read_more' => 'Akhrin dheeraad',
        'read_less' => 'Akhrin yar',
        'edit' => 'Wax ka beddel',
        'delete' => 'Tirtir',
        'delete_confirm' => 'Tirtir note-kan?',
        'note_deleted' => 'Note-ka si guul leh ayaa loo tirtiray.',
        'note_updated' => 'Note-ka si guul leh ayaa loo cusboonaysiiyay.',
        'note_added' => 'Note-ka si guul leh ayaa loogu daray.',
        'validation_error' => 'Fadlan dooro fasalka oo qor note.',
        'words' => 'ereyada',
        'characters' => 'xarfaha',
        'created' => 'La Aburay',
        'updated' => 'La Cusboonaysiiyay'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

/* =========================
   DELETE NOTE
========================= */
if (isset($_POST['delete_note'])) {
    $note_id = (int)$_POST['note_id'];

    $stmt = $conn->prepare("
        DELETE FROM class_notes 
        WHERE id = ? AND teacher_id = ?
    ");
    $stmt->bind_param("ii", $note_id, $teacher_id);
    $stmt->execute();

    $message = t('note_deleted');
}

/* =========================
   ADD / UPDATE NOTE
========================= */
if (isset($_POST['save_note'])) {
    $note_id  = (int)($_POST['note_id'] ?? 0);
    $class_id = (int)($_POST['class_id'] ?? 0);
    $note     = trim($_POST['note'] ?? '');

    if ($class_id > 0 && $note !== '') {

        if ($note_id > 0) {
            // UPDATE
            $stmt = $conn->prepare("
                UPDATE class_notes 
                SET class_id = ?, note = ?
                WHERE id = ? AND teacher_id = ?
            ");
            $stmt->bind_param("isii", $class_id, $note, $note_id, $teacher_id);
            $stmt->execute();

            $message = t('note_updated');
        } else {
            // INSERT
            $stmt = $conn->prepare("
                INSERT INTO class_notes (class_id, teacher_id, note)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis", $class_id, $teacher_id, $note);
            $stmt->execute();

            $message = t('note_added');
        }

    } else {
        $message = t('validation_error');
    }
}

/* =========================
   FETCH CLASSES
========================= */
$classes = [];
$res = $conn->query("
    SELECT DISTINCT c.id, c.name
    FROM classes c
    JOIN class_subjects cs ON cs.class_id = c.id
    WHERE cs.teacher_id = $teacher_id
    ORDER BY c.name
");
while ($row = $res->fetch_assoc()) {
    $classes[] = $row;
}

/* =========================
   FETCH NOTES
========================= */
$notes = [];
$res = $conn->query("
    SELECT n.*, c.name AS class_name
    FROM class_notes n
    LEFT JOIN classes c ON c.id = n.class_id
    WHERE n.teacher_id = $teacher_id
    ORDER BY n.created_at DESC
");
while ($row = $res->fetch_assoc()) {
    $notes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #7f8c8d;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 16px rgba(0,0,0,0.1);
            --border-radius: 8px;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
        }

        /* Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow-md);
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .page-header .lead {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 400;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stats-icon {
            font-size: 1.75rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .stats-card .fs-3 {
            font-size: 1.5rem !important;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stats-card .text-muted {
            font-size: 0.85rem;
            color: var(--secondary-color) !important;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 2.5rem;
            border: 1px solid var(--border-color);
        }

        .form-header {
            background: var(--light-bg);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .form-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.625rem 0.875rem;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-text {
            font-size: 0.8rem;
            color: var(--secondary-color);
            margin-top: 0.375rem;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            color: var(--secondary-color);
            border-color: var(--border-color);
        }

        .btn-outline-secondary:hover {
            /* background-color: var(--light-bg); */
            border-color: var(--secondary-color);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        /* Notes Container */
        .notes-container {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .notes-header {
            background: var(--light-bg);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .notes-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .notes-header .badge {
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.25rem 0.625rem;
        }

        .notes-header p {
            font-size: 0.85rem;
        }

        /* Note Cards */
        .note-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .note-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .note-card:last-child {
            margin-bottom: 0;
        }

        .note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.875rem;
            border-bottom: 1px solid var(--border-color);
        }

        .class-badge {
            background: var(--light-bg);
            color: var(--primary-color);
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .timestamp {
            color: var(--secondary-color);
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* Note Content */
        .note-content {
            color: #444;
            line-height: 1.7;
            margin-bottom: 1.25rem;
            font-size: 0.925rem;
        }

        .note-collapsible {
            cursor: pointer;
        }

        .read-toggle {
            font-size: 0.85rem;
            cursor: pointer;
            font-weight: 500;
            color: var(--accent-color) !important;
            display: inline-block;
            margin-left: 0.5rem;
        }

        .read-toggle:hover {
            text-decoration: underline;
        }

        /* Note Actions */
        .note-actions {
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .note-actions .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--secondary-color);
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1.5rem;
        }

        .empty-state h4 {
            font-size: 1.25rem;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            font-size: 0.9rem;
            max-width: 400px;
            margin: 0 auto 1.5rem;
        }

        /* Alert */
        .alert-custom {
            border-radius: var(--border-radius);
            border: 1px solid transparent;
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
        }

        .alert-info {
            background-color: #e8f4fd;
            border-color: #b6e0fe;
            color: #0c5460;
        }

        /* Dropdown */
        .dropdown-menu {
            border-radius: 6px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            font-size: 0.9rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            font-weight: 400;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 2rem 0;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .form-container, .notes-container {
                margin-left: -0.75rem;
                margin-right: -0.75rem;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .note-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .note-header .dropdown {
                align-self: flex-end;
            }
            
            .d-flex.gap-3 {
                flex-direction: column;
                gap: 0.75rem !important;
            }
            
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .row.g-4 {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .col-md-3 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .stats-card {
                padding: 1.25rem 1rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #ffffff;
                color: #000000;
            }
            
            .stats-card,
            .form-container,
            .notes-container,
            .note-card {
                background-color: #ffffff;
                border-color: #3a3d45;
            }
            
            .page-header {
                background: linear-gradient(135deg, #0385ff 0%, #6ab4ff 100%);
            }
            
            .form-header,
            .notes-header {
                background-color: #7a7cff;
                border-color: #3a3d45;
                color: black;
            }
            
            .form-control,
            .form-select {
                background-color: #2d3239;
                border-color: #3a3d45;
                color: #000000;
            }
            
            .form-label {
                color: #000000;
            }
            
            .note-content {
                color: #000000;
            }
            
            .class-badge {
                background-color: #c9c9c9;
                color: #d1d5db;
            }
            
            .alert-info {
                background-color: #1c3d5a;
                border-color: #2c5282;
                color: #90cdf4;
            }
        }
    </style>
</head>
<body>
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-3">
                        <i class="fas fa-sticky-note me-3"></i><?= t('dashboard_title') ?>
                    </h1>
                    <p class="mb-0"><?= t('dashboard_subtitle') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="fs-3 fw-bold"><?= count($notes) ?></div>
                    <div class="text-muted"><?= t('total_notes') ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="fs-3 fw-bold"><?= count($classes) ?></div>
                    <div class="text-muted"><?= t('classes') ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-pen"></i>
                    </div>
                    <div class="fs-3 fw-bold">
                        <?php
                        $totalChars = 0;
                        foreach ($notes as $n) {
                            $totalChars += strlen($n['note']);
                        }
                        $wordCount = round($totalChars / 5); // Approximate word count
                        echo number_format($wordCount);
                        ?>
                    </div>
                    <div class="text-muted"><?= t('words_written') ?></div>
                </div>
            </div>
           
        </div>

        <!-- Message Alert -->
        <?php if ($message): ?>
            <div class="alert alert-info alert-custom alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3"></i>
                    <div class="flex-grow-1"><?= htmlspecialchars($message) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Note Form -->
        <div class="form-container">
            <div class="form-header">
                <h3 class="mb-0" id="form-title">
                    <i class="fas fa-plus-circle me-2"></i><?= t('add_new_note') ?>
                </h3>
            </div>
            <div class="form-body">
                <form method="post" id="noteForm">
                    <input type="hidden" name="note_id" id="note_id">
                    
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-users me-2"></i><?= t('class') ?>
                        </label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            <option value="">-- <?= t('select_class') ?> --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= htmlspecialchars($c['id']) ?>">
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text"><?= t('class_instruction') ?></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-edit me-2"></i><?= t('note') ?>
                        </label>
                        <textarea name="note" id="note" class="form-control" rows="5" required 
                                  placeholder="<?= t('note_placeholder') ?>"></textarea>
                        <div class="form-text"><?= t('note_instruction') ?></div>
                        <small class="text-muted mt-1 d-block" id="charCount">0 <?= t('characters') ?></small>
                    </div>

                    <div class="d-flex gap-3">
                        <button name="save_note" class="btn btn-primary" id="saveBtn">
                            <i class="fas fa-save me-2"></i><?= t('save_note') ?>
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="cancelEdit">
                            <i class="fas fa-times me-2"></i><?= t('cancel') ?>
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i><?= t('clear') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notes List -->
        <div class="notes-container mt-5">
            <div class="notes-header">
                <h3 class="mb-0">
                    <i class="fas fa-list me-2"></i><?= t('my_notes') ?>
                    <span class="badge bg-primary rounded-pill ms-2"><?= count($notes) ?></span>
                </h3>
                <p class="mb-0 mt-2"><?= t('all_notes') ?></p>
            </div>
            
            <div class="p-4">
                <?php if (empty($notes)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h4 class="mb-3"><?= t('no_notes_found') ?></h4>
                        <p class="mb-4"><?= t('no_notes_message') ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notes as $n): ?>
                        <div class="note-card" id="note-<?= htmlspecialchars($n['id']) ?>">
                            <div class="note-header">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <span class="class-badge">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>
                                        <?= htmlspecialchars($n['class_name']) ?>
                                    </span>
                                    <div class="timestamp">
                                        <i class="fas fa-clock"></i>
                                        <?= date('d M Y, H:i', strtotime($n['created_at'])) ?>
                                        <?php if ($n['created_at'] && $n['created_at'] != $n['created_at']): ?>
                                            <span class="ms-2">(<?= t('updated') ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                            type="button" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" 
                                                    onclick="editNote(
                                                        '<?= $n['id'] ?>',
                                                        '<?= $n['class_id'] ?>',
                                                        `<?= htmlspecialchars($n['note'], ENT_QUOTES) ?>`
                                                    )">
                                                <i class="fas fa-edit me-2"></i><?= t('edit') ?>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="post" 
                                                  onsubmit="return confirm('<?= t('delete_confirm') ?>')" 
                                                  class="d-inline">
                                                <input type="hidden" name="note_id" value="<?= htmlspecialchars($n['id']) ?>">
                                                <button type="submit" 
                                                        name="delete_note" 
                                                        class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i><?= t('delete') ?>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="note-body">
                                <?php
                                $noteText  = trim($n['note']);
                                $words     = preg_split('/\s+/', $noteText);
                                $wordCount = count($words);

                                $preview   = implode(' ', array_slice($words, 0, 50));
                                $isLong    = $wordCount > 50;
                                ?>

                                <div class="note-content <?= $isLong ? 'note-collapsible' : '' ?>">

                                    <?php if ($isLong): ?>
                                        <span class="note-preview">
                                            <?= nl2br(htmlspecialchars($preview)) ?>…
                                            <span class="read-toggle"><?= t('read_more') ?></span>
                                        </span>

                                        <span class="note-full d-none">
                                            <?= nl2br(htmlspecialchars($noteText)) ?>
                                            <span class="read-toggle"><?= t('read_less') ?></span>
                                        </span>
                                    <?php else: ?>
                                        <?= nl2br(htmlspecialchars($noteText)) ?>
                                    <?php endif; ?>

                                </div>
                                
                                <div class="note-actions">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-warning"
                                                onclick="editNote(
                                                    '<?= $n['id'] ?>',
                                                    '<?= $n['class_id'] ?>',
                                                    `<?= htmlspecialchars($n['note'], ENT_QUOTES) ?>`
                                                )">
                                            <i class="fas fa-edit me-1"></i><?= t('edit') ?>
                                        </button>
                                        
                                        <form method="post" 
                                              onsubmit="return confirm('<?= t('delete_confirm') ?>')" 
                                              class="d-inline">
                                            <input type="hidden" name="note_id" value="<?= htmlspecialchars($n['id']) ?>">
                                            <button name="delete_note" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash me-1"></i><?= t('delete') ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit note function
        function editNote(id, classId, note) {
            // Set form values
            document.getElementById('note_id').value = id;
            document.getElementById('class_id').value = classId;
            document.getElementById('note').value = note;
            updateCharCount();
            
            // Update UI
            document.getElementById('form-title').innerHTML = '<i class="fas fa-edit me-2"></i><?= t('edit_note') ?>';
            document.getElementById('saveBtn').innerHTML = '<i class="fas fa-sync me-2"></i><?= t('update_note') ?>';
            document.getElementById('cancelEdit').classList.remove('d-none');
            
            // Scroll to form with smooth animation
            const formContainer = document.querySelector('.form-container');
            formContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
            
            // Highlight the form
            formContainer.style.boxShadow = '0 0 0 3px rgba(52, 152, 219, 0.3)';
            setTimeout(() => {
                formContainer.style.boxShadow = '';
            }, 1500);
        }
        
        // Cancel edit function
        document.getElementById('cancelEdit').onclick = function () {
            document.getElementById('noteForm').reset();
            document.getElementById('note_id').value = '';
            document.getElementById('form-title').innerHTML = '<i class="fas fa-plus-circle me-2"></i><?= t('add_new_note') ?>';
            document.getElementById('saveBtn').innerHTML = '<i class="fas fa-save me-2"></i><?= t('save_note') ?>';
            this.classList.add('d-none');
            updateCharCount();
        };
        
        // Character count
        function updateCharCount() {
            const textarea = document.getElementById('note');
            const charCount = textarea.value.length;
            const wordCount = textarea.value.trim().split(/\s+/).filter(word => word.length > 0).length;
            document.getElementById('charCount').textContent = 
                `${charCount} <?= t('characters') ?>, ${wordCount} <?= t('words') ?>`;
        }
        
        document.getElementById('note').addEventListener('input', updateCharCount);
        updateCharCount();
        
        // Auto-resize textarea
        const textarea = document.getElementById('note');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Initialize textarea height
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
        
        // Form validation
        document.getElementById('noteForm').addEventListener('submit', function(event) {
            const classId = document.getElementById('class_id').value;
            const noteContent = document.getElementById('note').value.trim();
            
            if (!classId || !noteContent) {
                event.preventDefault();
                
                // Remove existing error alerts
                const existingAlerts = this.querySelectorAll('.alert-danger');
                existingAlerts.forEach(alert => alert.remove());
                
                // Highlight empty fields
                if (!classId) {
                    document.getElementById('class_id').style.borderColor = '#e74c3c';
                }
                if (!noteContent) {
                    document.getElementById('note').style.borderColor = '#e74c3c';
                }
                
                // Show error message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-custom mt-3';
                alertDiv.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3"></i>
                        <div><?= t('validation_error') ?></div>
                    </div>
                `;
                
                this.insertBefore(alertDiv, this.querySelector('.d-flex'));
            }
        });
        
        // Clear validation on input
        document.getElementById('class_id').addEventListener('change', function() {
            this.style.borderColor = '';
        });
        
        document.getElementById('note').addEventListener('input', function() {
            this.style.borderColor = '';
        });
        
        // Toggle note content
        function toggleNote(el) {
            const preview = el.querySelector('.note-preview');
            const full = el.querySelector('.note-full');
            
            if (!preview || !full) return;
            
            preview.classList.toggle('d-none');
            full.classList.toggle('d-none');
        }
        
        // Add click handlers to collapsible notes
        document.querySelectorAll('.note-collapsible').forEach(note => {
            note.addEventListener('click', function(e) {
                // Only toggle if clicking on the note content, not buttons
                if (!e.target.closest('button') && !e.target.closest('form')) {
                    toggleNote(this);
                }
            });
        });
    </script>
</body>
</html>