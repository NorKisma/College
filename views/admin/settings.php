<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT * FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Get all active languages
$langs = $conn->query("SELECT * FROM languages WHERE is_active = 1 ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

// Translation array for this page
$translations = [
    1 => [ // English
        'page_title' => 'Settings Panel | Admin',
        'header_title' => 'Admin Settings',
        'header_subtitle' => 'Manage your application configuration and appearance',
        'success_message' => 'Settings updated successfully',
        
        // General Settings
        'general_settings' => 'General Settings',
        'default_language' => 'Default Language',
        'panel_name' => 'Panel Name',
        'panel_name_placeholder' => 'Enter your application name',
        'display_type' => 'Display Type',
        'display_text' => 'Text',
        'display_logo' => 'Logo',
        
        // Logo Settings
        'logo_settings' => 'Logo Settings',
        'upload_logo_label' => 'Upload Logo (JPG, PNG, WEBP - Max 5MB)',
        'choose_logo_file' => 'Choose Logo File',
        
        // Slideshow Settings
        'slideshow_settings' => 'Login Background Slideshow',
        'slideshow_description' => 'Upload images for the login page background slideshow (Maximum 10 images allowed).',
        'add_slides_images' => 'Add Slideshow Images',
        'select_multiple_images' => 'Select Multiple Images',
        'no_slides_message' => 'No slideshow images uploaded yet. Add some images to create a login slideshow.',
        
        // Buttons
        'save_all_settings' => 'Save All Settings',
        'upload_selected_images' => 'Upload Selected Images',
        'delete_slide_confirmation' => 'Are you sure you want to delete this slide? This action cannot be undone.',
        'saving_changes' => 'Saving Changes...',
        
        // Theme
        'theme_dark' => 'Dark Mode',
        'theme_light' => 'Light Mode',
    ],
    2 => [ // Arabic
        'page_title' => 'لوحة الإعدادات | الأدمن',
        'header_title' => 'إعدادات الأدمن',
        'header_subtitle' => 'إدارة إعدادات التطبيق والمظهر',
        'success_message' => 'تم تحديث الإعدادات بنجاح',
        
        // General Settings
        'general_settings' => 'الإعدادات العامة',
        'default_language' => 'اللغة الافتراضية',
        'panel_name' => 'اسم اللوحة',
        'panel_name_placeholder' => 'أدخل اسم التطبيق',
        'display_type' => 'نوع العرض',
        'display_text' => 'نص',
        'display_logo' => 'شعار',
        
        // Logo Settings
        'logo_settings' => 'إعدادات الشعار',
        'upload_logo_label' => 'رفع الشعار (JPG, PNG, WEBP - الحد الأقصى 5 ميجابايت)',
        'choose_logo_file' => 'اختر ملف الشعار',
        
        // Slideshow Settings
        'slideshow_settings' => 'عرض الشرائح لخلفية تسجيل الدخول',
        'slideshow_description' => 'قم برفع صور لخلفية صفحة تسجيل الدخول (الحد الأقصى 10 صور).',
        'add_slides_images' => 'إضافة صور العرض',
        'select_multiple_images' => 'اختر صور متعددة',
        'no_slides_message' => 'لا توجد صور للعرض بعد. أضف بعض الصور لإنشاء عرض شرائح لتسجيل الدخول.',
        
        // Buttons
        'save_all_settings' => 'حفظ جميع الإعدادات',
        'upload_selected_images' => 'رفع الصور المحددة',
        'delete_slide_confirmation' => 'هل أنت متأكد أنك تريد حذف هذه الشريحة؟ لا يمكن التراجع عن هذا الإجراء.',
        'saving_changes' => 'جاري حفظ التغييرات...',
        
        // Theme
        'theme_dark' => 'الوضع الداكن',
        'theme_light' => 'الوضع الفاتح',
    ]
];

// Translation helper function
function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

// ==================== GET EXISTING SETTINGS ====================
$slides = $conn->query("SELECT * FROM login_slideshow ORDER BY position ASC")->fetch_all(MYSQLI_ASSOC);
$themeRow = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1")->fetch_assoc();
$themeMode = $themeRow['mode'] ?? 1;
$isDark = $themeMode == 0;

// ==================== HANDLE POST REQUEST ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app_name = $_POST['app_name'] ?? '';
    $show_type = $_POST['show_type'] ?? 'text';
    $theme_mode = $_POST['theme_mode'] ?? 1;
    $logo_path = $settings['logo_path'];
    $language_id = $_POST['language_id'] ?? $currentLangId;

    $stmt = $conn->prepare("UPDATE app_settings SET app_name=?, logo_path=?, show_type=?, language_id=? WHERE id=?");
    $stmt->bind_param("sssii", $app_name, $logo_path, $show_type, $language_id, $settings['id']);
    $stmt->execute();

    // ===== UPLOAD LOGO =====
    if (!empty($_FILES['logo']['name'])) {
        $uploadDir = 'uploads/logo/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . uniqid() . '_' . basename($_FILES['logo']['name']);
        $target = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg','image/jpg','image/png','image/webp'];
        if (in_array($_FILES['logo']['type'], $allowedTypes) && $_FILES['logo']['size'] <= 5*1024*1024) {
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                $logo_path = 'views/admin/uploads/logo/' . $fileName;
            }
        }
    }

    // ===== UPLOAD SLIDES =====
    if (!empty($_FILES['slide_image']['name'][0])) {
        $countRes = $conn->query("SELECT COUNT(*) AS total FROM login_slideshow");
        $countRow = $countRes->fetch_assoc();
        $currentTotal = $countRow['total'];

        $dir = "uploads/slideshow/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        foreach ($_FILES['slide_image']['name'] as $key => $name) {
            if ($currentTotal >= 5) break;

            $tmp_name = $_FILES['slide_image']['tmp_name'][$key];
            $fileName = time() . '_' . uniqid() . '_' . basename($name);
            $target = $dir . $fileName;

            $allowedTypes = ['image/jpeg','image/jpg','image/png','image/webp'];
            if (in_array($_FILES['slide_image']['type'][$key], $allowedTypes) && $_FILES['slide_image']['size'][$key] <= 5*1024*1024) {
                if (move_uploaded_file($tmp_name, $target)) {
                    $path = "views/admin/uploads/slideshow/" . $fileName;
                    $stmt = $conn->prepare("INSERT INTO login_slideshow (image_path, position) VALUES (?, ?)");
                    $stmt->bind_param("si", $path, $currentTotal);
                    $stmt->execute();
                    $currentTotal++;
                }
            }
        }
    }

    // ===== UPDATE SETTINGS =====
    $stmt = $conn->prepare("UPDATE app_settings SET app_name=?, logo_path=?, show_type=? WHERE id=?");
    $stmt->bind_param("sssi", $app_name, $logo_path, $show_type, $settings['id']);
    $stmt->execute();

    header("Location: settings.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ====== ROOT VARIABLES ====== */
        :root {
            /* Light Theme */
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-card: rgba(255, 255, 255, 0.95);
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #adb5bd;
            --border-color: #e9ecef;
            --border-light: #dee2e6;
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --success-color: #2ecc71;
            --success-light: rgba(46, 204, 113, 0.1);
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.12);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        /* Dark Theme */
        body.dark-mode {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --bg-card: rgba(30, 30, 30, 0.95);
            --text-primary: #e0e0e0;
            --text-secondary: #9e9e9e;
            --text-muted: #757575;
            --border-color: #2d2d2d;
            --border-light: #3d3d3d;
            --primary-color: #5a7dff;
            --primary-hover: #6b8cff;
            --success-color: #4cd964;
            --success-light: rgba(76, 217, 100, 0.1);
            --danger-color: #ff6b6b;
            --warning-color: #ffd166;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.5);
            --glass-bg: rgba(30, 30, 30, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        /* ====== BASE STYLES ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: var(--transition);
            min-height: 100vh;
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }

        /* ====== TYPOGRAPHY ====== */
        h1 {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary-color), #8a2be2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        h2 {
            font-size: clamp(1.25rem, 3vw, 1.75rem);
            font-weight: 600;
            margin-bottom: 20px;
        }

        p {
            color: var(--text-secondary);
            font-size: clamp(0.9rem, 2vw, 1rem);
            margin-bottom: 16px;
        }

        /* ====== HEADER ====== */
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .header-left h1 {
            font-size: clamp(1.5rem, 5vw, 2rem);
        }

        .header-left p {
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            opacity: 0.8;
        }

        /* ====== SUCCESS MESSAGE ====== */
        .success-message {
            background: var(--success-light);
            border-left: 4px solid var(--success-color);
            padding: 16px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            animation: slideIn 0.5s ease;
        }

        .success-message i {
            color: var(--success-color);
            font-size: 1.25rem;
        }

        .success-message span {
            color: var(--success-color);
            font-weight: 500;
        }

        /* ====== CARDS ====== */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: clamp(20px, 4vw, 30px);
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #8a2be2);
        }

        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
        }

        .card-title i {
            color: var(--primary-color);
            font-size: 1.5rem;
            background: rgba(67, 97, 238, 0.1);
            padding: 12px;
            border-radius: 12px;
        }

        .card-title span {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* ====== FORM ELEMENTS ====== */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 1rem;
            transition: var(--transition);
            -webkit-appearance: none;
            appearance: none;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 50px;
        }

        /* ====== FILE UPLOAD ====== */
        .file-upload-wrapper {
            position: relative;
            margin-top: 8px;
        }

        .file-upload-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            background: var(--primary-color);
            color: white;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid transparent;
            font-weight: 500;
        }

        .file-upload-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .file-upload-btn i {
            font-size: 1.25rem;
        }

        .file-upload-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 10;
        }

        /* ====== IMAGE PREVIEW ====== */
        .preview-image {
            margin-top: 20px;
            display: inline-block;
        }

        .preview-image img {
            max-width: 150px;
            height: auto;
            border-radius: var(--radius-md);
            border: 2px solid var(--border-color);
            padding: 4px;
            background: white;
        }

        /* ====== BUTTONS ====== */
        button[type="submit"] {
            width: 100%;
            max-width: 300px;
            margin: 30px auto;
            display: block;
        }

        .btn-primary, .btn-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 32px;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #8a2be2);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--bg-card);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* ====== SLIDESHOW GRID ====== */
        .slideshow-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .slide-item {
            position: relative;
            border-radius: var(--radius-md);
            overflow: hidden;
            aspect-ratio: 16/9;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .slide-item:hover {
            transform: scale(1.03);
            box-shadow: var(--shadow-md);
        }

        .slide-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger-color);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            opacity: 0;
            transform: scale(0.8);
            transition: var(--transition);
        }

        .slide-item:hover .delete-btn {
            opacity: 1;
            transform: scale(1);
        }

        .delete-btn:hover {
            background: #ff4757;
            transform: scale(1.1);
        }

        /* ====== DIVIDER ====== */
        .divider {
            height: 1px;
            background: var(--border-color);
            margin: 40px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 5px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        /* ====== ANIMATIONS ====== */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        /* ====== RESPONSIVE DESIGN ====== */
        /* Extra Small Devices (Phones, < 576px) */
        @media (max-width: 575.98px) {
            .container {
                padding: 12px;
            }
            
            .glass-card {
                padding: 20px 16px;
                margin-bottom: 20px;
            }
            
            .card-title {
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }
            
            .card-title i {
                padding: 10px;
            }
            
            .slideshow-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 12px;
            }
            
            .form-control {
                padding: 12px;
                font-size: 16px; /* Prevents iOS zoom */
            }
            
            .btn-primary, .btn-secondary {
                padding: 14px 20px;
                font-size: 0.95rem;
            }
            
            .file-upload-btn {
                padding: 14px 16px;
                font-size: 0.9rem;
            }
            
            .preview-image img {
                max-width: 120px;
            }
        }

        /* Small Devices (Landscape Phones, 576px - 767px) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .container {
                padding: 16px;
            }
            
            .slideshow-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 16px;
            }
            
            .glass-card {
                padding: 24px 20px;
            }
        }

        /* Medium Devices (Tablets, 768px - 991px) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .container {
                padding: 20px;
            }
            
            .slideshow-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 20px;
            }
        }

        /* Large Devices (Desktops, 992px - 1199px) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .slideshow-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        /* Extra Large Devices (Large Desktops, 1200px+) */
        @media (min-width: 1200px) {
            .container {
                padding: 30px;
            }
        }

        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .delete-btn {
                opacity: 1;
                transform: scale(1);
            }
            
            .file-upload-btn:active {
                transform: scale(0.98);
            }
            
            .btn-primary:active, .btn-secondary:active {
                transform: scale(0.98);
            }
            
            .slide-item:active {
                transform: scale(0.98);
            }
        }

        /* High Contrast Mode Support */
        @media (prefers-contrast: high) {
            :root {
                --primary-color: #0044cc;
                --success-color: #008000;
                --danger-color: #cc0000;
            }
        }

        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            .glass-card:hover {
                transform: none;
            }
        }

        /* Dark Mode Auto-detection */
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) {
                background-color: #121212;
                color: #e0e0e0;
            }
        }

        /* Print Styles */
        @media print {
            .glass-card {
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .btn-primary, .btn-secondary, .file-upload-wrapper, .delete-btn {
                display: none !important;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            border: 3px solid var(--border-color);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Accessibility */
        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus Styles for Keyboard Navigation */
        :focus-visible {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
    </style>
</head>
<body class="<?php echo $isDark ? 'dark-mode' : 'light-mode'; ?>">
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-left">
                <h1><?= t('header_title') ?></h1>
                <p><?= t('header_subtitle') ?></p>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <span><?= t('success_message') ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- General Settings Card -->
            <div class="glass-card">
                <div class="card-title">
                    <i class="fas fa-cog"></i>
                    <span><?= t('general_settings') ?></span>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= t('default_language') ?></label>
                    <select name="language_id" class="form-control form-select">
                        <?php foreach ($langs as $lang): ?>
                            <option value="<?= $lang['id'] ?>" <?= $lang['id'] == $currentLangId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lang['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Panel Name -->
                <div class="form-group">
                    <label class="form-label"><?= t('panel_name') ?></label>
                    <input type="text" name="app_name" value="<?= htmlspecialchars($settings['app_name']) ?>" 
                           class="form-control" placeholder="<?= t('panel_name_placeholder') ?>">
                </div>

                <!-- Display Type -->
                <!-- Uncomment if needed
                <div class="form-group">
                    <label class="form-label"><?= t('display_type') ?></label>
                    <select name="show_type" class="form-control form-select">
                        <option value="text" <?= $settings['show_type']=='text'?'selected':'' ?>><?= t('display_text') ?></option>
                        <option value="logo" <?= $settings['show_type']=='logo'?'selected':'' ?>><?= t('display_logo') ?></option>
                    </select>
                </div>
                -->
            </div>

            <!-- Logo Settings Card -->
            <div class="glass-card">
                <div class="card-title">
                    <i class="fas fa-image"></i>
                    <span><?= t('logo_settings') ?></span>
                </div>

                <!-- Current Logo Preview -->
                <?php if (!empty($settings['logo_path'])): ?>
                    <div class="preview-image">
                        <img src="/College/<?= htmlspecialchars($settings['logo_path']) ?>" alt="Current Logo">
                    </div>
                <?php endif; ?>

                <!-- Logo Upload -->
                <div class="form-group">
                    <label class="form-label"><?= t('upload_logo_label') ?></label>
                    <div class="file-upload-wrapper">
                        <div class="file-upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span><?= t('choose_logo_file') ?></span>
                        </div>
                        <input type="file" name="logo" accept="image/*" class="file-upload-input">
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i>
                <span><?= t('save_all_settings') ?></span>
            </button>

            <div class="divider"></div>

            <!-- Slideshow Settings Card -->
            <div class="glass-card">
                <div class="card-title">
                    <i class="fas fa-sliders-h"></i>
                    <span><?= t('slideshow_settings') ?></span>
                </div>

                <p><?= t('slideshow_description') ?></p>

                <!-- Slideshow Upload -->
                <div class="form-group">
                    <label class="form-label"><?= t('add_slides_images') ?></label>
                    <div class="file-upload-wrapper">
                        <div class="file-upload-btn">
                            <i class="fas fa-images"></i>
                            <span><?= t('select_multiple_images') ?></span>
                        </div>
                        <input type="file" name="slide_image[]" accept="image/*" multiple class="file-upload-input">
                    </div>
                </div>

                <!-- Upload Slides Button -->
                <button type="submit" class="btn-secondary">
                    <i class="fas fa-upload"></i>
                    <span><?= t('upload_selected_images') ?></span>
                </button>

                <!-- Current Slides Grid -->
                <?php if (!empty($slides)): ?>
                    <div class="slideshow-grid">
                        <?php foreach ($slides as $slide): ?>
                            <div class="slide-item">
                                <img src="/College/<?= $slide['image_path'] ?>" alt="Slide Image">
                                <a href="delete_slide.php?id=<?= $slide['id'] ?>" class="delete-btn" title="Delete">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; margin-top: 30px; padding: 20px; border: 2px dashed var(--border-color); border-radius: 12px; color: var(--text-secondary);">
                        <?= t('no_slides_message') ?>
                    </p>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        // Enhanced JavaScript with mobile optimizations
        document.addEventListener('DOMContentLoaded', function() {
            // File input visual feedback with mobile optimization
            document.querySelectorAll('.file-upload-input').forEach(input => {
                input.addEventListener('change', function(e) {
                    const wrapper = this.closest('.file-upload-wrapper');
                    const button = wrapper.querySelector('.file-upload-btn span');
                    
                    if (this.files && this.files.length > 0) {
                        if (this.files.length === 1) {
                            button.textContent = this.files[0].name.length > 20 
                                ? this.files[0].name.substring(0, 20) + '...' 
                                : this.files[0].name;
                        } else {
                            button.textContent = `${this.files.length} files selected`;
                        }
                        
                        // Visual feedback optimized for mobile
                        const btn = wrapper.querySelector('.file-upload-btn');
                        btn.style.background = 'var(--success-color)';
                        
                        // Reset after 1.5 seconds (shorter for mobile)
                        setTimeout(() => {
                            btn.style.background = '';
                        }, 1500);
                    }
                });
            });

            // Form submission with loading state
            document.querySelector('form').addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-primary');
                if (submitBtn) {
                    const originalHTML = submitBtn.innerHTML;
                    
                    // Show loading state optimized for mobile
                    submitBtn.innerHTML = `
                        <i class="fas fa-spinner fa-spin"></i>
                        <span><?= t('saving_changes') ?></span>
                    `;
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Revert after 5 seconds (for error cases)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalHTML;
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    }, 5000);
                }
            });

            // Delete confirmation with vibration feedback on mobile
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    if (!confirm('<?= t('delete_slide_confirmation') ?>')) {
                        e.preventDefault();
                        // Vibration feedback for mobile
                        if (navigator.vibrate) {
                            navigator.vibrate(50);
                        }
                    }
                });
            });

            // Mobile touch optimizations
            let touchStartY = 0;
            let touchEndY = 0;

            document.addEventListener('touchstart', function(e) {
                touchStartY = e.changedTouches[0].screenY;
            }, { passive: true });

            document.addEventListener('touchend', function(e) {
                touchEndY = e.changedTouches[0].screenY;
                // Prevent pull-to-refresh on forms
                if (touchStartY - touchEndY > 100) {
                    if (document.activeElement.tagName === 'INPUT' || 
                        document.activeElement.tagName === 'SELECT' || 
                        document.activeElement.tagName === 'TEXTAREA') {
                        e.preventDefault();
                    }
                }
            }, { passive: false });

            // Prevent zoom on input focus for iOS
            document.querySelectorAll('input, select, textarea').forEach(el => {
                el.addEventListener('touchstart', function() {
                    this.style.fontSize = '16px'; // Prevent iOS zoom
                });
                
                el.addEventListener('blur', function() {
                    this.style.fontSize = '';
                });
            });

            // Handle viewport height changes on mobile
            function setVH() {
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            }

            setVH();
            window.addEventListener('resize', setVH);
            window.addEventListener('orientationchange', setVH);

            // Handle keyboard show/hide on mobile
            if ('visualViewport' in window) {
                const visualViewport = window.visualViewport;
                
                visualViewport.addEventListener('resize', function() {
                    // Scroll active element into view when keyboard appears
                    if (document.activeElement) {
                        document.activeElement.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                });
            }

            // Performance optimization: Lazy load images
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }

            // Error handling for file uploads
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('error', function(e) {
                    console.error('File input error:', e);
                    // Show user-friendly error message
                    const wrapper = this.closest('.file-upload-wrapper');
                    const button = wrapper.querySelector('.file-upload-btn span');
                    button.textContent = 'Error loading file';
                    button.style.color = 'var(--danger-color)';
                    
                    setTimeout(() => {
                        button.textContent = 'Choose File';
                        button.style.color = '';
                    }, 3000);
                });
            });
        });

        // Handle offline/online status
        window.addEventListener('online', function() {
            showToast('Back online', 'success');
        });

        window.addEventListener('offline', function() {
            showToast('You are offline. Changes will sync when back online.', 'warning');
        });

        function showToast(message, type) {
            // Simple toast notification for mobile
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: ${type === 'success' ? 'var(--success-color)' : 'var(--warning-color)'};
                color: white;
                padding: 12px 24px;
                border-radius: 8px;
                z-index: 10000;
                font-weight: 500;
                box-shadow: var(--shadow-lg);
                animation: slideUp 0.3s ease;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideDown 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>