<?php
require_once __DIR__ . '/../../config/sessions.php';
require_once __DIR__ . '/../../config/db.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Navigation
        'page_title' => 'Admin Dashboard',
        'nav_dashboard' => 'Dashboard',
        'nav_users' => 'Users',
        'nav_courses' => 'Courses',
        'nav_classes' => 'Classes',
        'nav_teachers' => 'Teachers',
        'nav_students' => 'Students',
        'nav_attendance' => 'Attendance',
        'nav_finance' => 'Finance',
        'nav_expenses' => 'Expenses',
        'nav_exams' => 'Exams',
        'nav_messages' => 'Messages',
        'nav_alumni' => 'Alumni',
        'nav_settings' => 'Settings',
        'nav_logout' => 'Logout',
        
        // User Interface
        'toggle_sidebar' => 'Toggle Sidebar',
        'toggle_theme' => 'Toggle Theme',
        'open_settings' => 'Settings',
        'scroll_to_top' => 'Scroll to Top',
        'mobile_menu' => 'Mobile Menu',
        
        // Status Messages
        'loading' => 'Loading...',
        
        // Login Redirect
        'login_required' => 'Login required',
    ],
    2 => [ // Arabic
        // Page Title & Navigation
        'page_title' => 'لوحة تحكم المسؤول',
        'nav_dashboard' => 'لوحة التحكم',
        'nav_users' => 'المستخدمين',
        'nav_courses' => 'المواد',
        'nav_classes' => 'الفصول',
        'nav_teachers' => 'المعلمين',
        'nav_students' => 'الطلاب',
        'nav_attendance' => 'الحضور',
        'nav_finance' => 'المالية',
        'nav_expenses' => 'المصروفات',
        'nav_exams' => 'الامتحانات',
        'nav_messages' => 'الرسائل',
        'nav_alumni' => 'الخريجين',
        'nav_settings' => 'الإعدادات',
        'nav_logout' => 'تسجيل الخروج',
        
        // User Interface
        'toggle_sidebar' => 'تبديل الشريط الجانبي',
        'toggle_theme' => 'تبديل السمة',
        'open_settings' => 'الإعدادات',
        'scroll_to_top' => 'التمرير إلى الأعلى',
        'mobile_menu' => 'قائمة الجوال',
        
        // Status Messages
        'loading' => 'جاري التحميل...',
        
        // Login Redirect
        'login_required' => 'مطلوب تسجيل الدخول',
    ]
];

// Translation helper function
function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

/* ---- CURRENT LOGGED-IN USER ---- */
$logged_in_user_id = $_SESSION['user_id'] ?? null;

if (!$logged_in_user_id) {
    // Not logged in → redirect or block
    header('Location: /College/login.php');
    exit;
}

function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}

function is_member() {
    return ($_SESSION['role'] ?? '') === 'member';
}

/* ---- READ APP SETTINGS ---- */
$settingsRes = $conn->query("SELECT * FROM app_settings ORDER BY id DESC LIMIT 1");
$app = $settingsRes ? $settingsRes->fetch_assoc() : [];

$appName  = $app['app_name'] ?? 'Admin Panel';
$logoPath = $app['logo_path'] ?? null;
$showType = $app['show_type'] ?? 'text';

/* ---- READ THEME FROM DB ---- */
$resTheme = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1");
$rowTheme = $resTheme ? $resTheme->fetch_assoc() : ['mode' => 1];
$theme    = (int)$rowTheme['mode']; // 0 = dark, 1 = light
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('page_title') ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>
                * { 
                    box-sizing: border-box; 
                    margin: 0; 
                    padding: 0; 
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    transition: background-color 0.3s, color 0.3s, transform 0.2s, opacity 0.3s;
                }

                :root {
                    /* Dark Theme (Default) */
                    --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    --sidebar-bg: rgba(15, 23, 42, 0.85);
                    --topbar-bg: rgba(15, 23, 42, 0.95);
                    --card-bg: rgba(255, 255, 255, 0.05);
                    --glass-bg: rgba(255, 255, 255, 0.08);
                    --glass-border: rgba(255, 255, 255, 0.1);
                    --text-primary: #f8fafc;
                    --text-secondary: #94a3b8;
                    --accent-primary: #3b82f6;
                    --accent-secondary: #8b5cf6;
                    --accent-hover: rgba(59, 130, 246, 0.15);
                    --shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                    --shadow-light: 0 4px 12px rgba(0, 0, 0, 0.1);
                    --radius-lg: 20px;
                    --radius-md: 12px;
                    --radius-sm: 8px;
                }

                /* Light Theme */
                body.light {
                    --bg-gradient: linear-gradient(135deg, #f0f4ff 0%, #ffffffff 100%);
                    --sidebar-bg: rgba(255, 255, 255, 0.95);
                    --topbar-bg: rgba(255, 255, 255, 0.98);
                    --card-bg: rgba(255, 255, 255, 0.7);
                    --glass-bg: rgba(255, 255, 255, 0.6);
                    --glass-border: rgba(0, 0, 0, 0.05);
                    --text-primary: #0f172a;
                    --text-secondary: #64748b;
                    --accent-primary: #2563eb;
                    --accent-secondary: #7c3aed;
                    --accent-hover: rgba(37, 99, 235, 0.1);
                    --shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
                    --shadow-light: 0 4px 12px rgba(0, 0, 0, 0.03);
                }

                body {
                    background: #ffffffff;
                    color: var(--text-primary);
                    min-height: 100vh;
                    overflow-x: hidden;
                    transition: background 0.5s ease;
                }

                /* Glassmorphism Effect */
                .glass {
                    background: var(--glass-bg);
                    backdrop-filter: blur(20px) saturate(180%);
                    -webkit-backdrop-filter: blur(20px) saturate(180%);
                    border: 1px solid var(--glass-border);
                    box-shadow: var(--shadow);
                }

                /* Sidebar - Modern Design with RTL support */
                .sidebar {
                    position: fixed;
                    <?= $currentLangId == 2 ? 'right: 0;' : 'left: 0;' ?>
                    top: 0;
                    width: 200px;
                    height: 100vh;
                    background: var(--sidebar-bg);
                    backdrop-filter: blur(20px);
                    padding: 30px 0;
                    z-index: 1000;
                    border-<?= $currentLangId == 2 ? 'left' : 'right' ?>: 1px solid var(--glass-border);
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .sidebar.collapsed {
                    width: 100px;
                }

                .sidebar-header {
                    padding: 0 25px 30px;
                    border-bottom: 1px solid var(--glass-border);
                    margin-bottom: 20px;
                    text-align: <?= $currentLangId == 2 ? 'right' : 'left' ?>;
                }

                .sidebar h2 {
                    font-size: 1.8rem;
                    font-weight: 800;
                    background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    letter-spacing: -0.5px;
                    transition: opacity 0.3s;
                    text-align: <?= $currentLangId == 2 ? 'right' : 'left' ?>;
                }

                .sidebar.collapsed h2 {
                    opacity: 0;
                }

                .sidebar-nav {
                    flex: 1;
                    padding: 0 15px;
                    overflow-y: auto;
                    scrollbar-width: thin;
                    scrollbar-color: var(--accent-primary) transparent;
                }

                .sidebar-nav::-webkit-scrollbar {
                    width: 4px;
                }

                .sidebar-nav::-webkit-scrollbar-thumb {
                    background: var(--accent-primary);
                    border-radius: 10px;
                }

                .nav-item {
                    display: flex;
                    align-items: center;
                    padding: 14px 20px;
                    margin-bottom: 8px;
                    color: var(--text-secondary);
                    text-decoration: none;
                    border-radius: var(--radius-md);
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    flex-direction: <?= $currentLangId == 2 ? 'row-reverse' : 'row' ?>;
                }

                .nav-item:hover {
                    background: var(--accent-hover);
                    color: var(--accent-primary);
                    transform: translateX(<?= $currentLangId == 2 ? '-5px' : '5px' ?>);
                }

                .nav-item.active {
                    background: linear-gradient(<?= $currentLangId == 2 ? '-90deg' : '90deg' ?>, var(--accent-primary), transparent);
                    color: var(--text-primary);
                    font-weight: 600;
                    box-shadow: var(--shadow-light);
                }

                .nav-item i {
                    font-size: 1.2rem;
                    width: 24px;
                    text-align: center;
                    <?= $currentLangId == 2 ? 'margin-left: 16px; margin-right: 0;' : 'margin-right: 16px; margin-left: 0;' ?>
                    flex-shrink: 0;
                }

                .nav-item span {
                    font-size: 0.95rem;
                    font-weight: 500;
                    white-space: nowrap;
                    transition: opacity 0.3s;
                }

                .sidebar.collapsed .nav-item span {
                    opacity: 0;
                    width: 0;
                }

                .nav-item.active::before {
                    content: '';
                    position: absolute;
                    <?= $currentLangId == 2 ? 'right: 0;' : 'left: 0;' ?>
                    top: 0;
                    height: 100%;
                    width: 3px;
                    background: var(--accent-primary);
                    border-radius: <?= $currentLangId == 2 ? 'var(--radius-sm) 0 0 var(--radius-sm)' : '0 var(--radius-sm) var(--radius-sm) 0' ?>;
                }

                .sidebar-footer {
                    padding: 20px 25px 0;
                    border-top: 1px solid var(--glass-border);
                    text-align: <?= $currentLangId == 2 ? 'right' : 'left' ?>;
                }

                .logout-btn {
                    display: flex;
                    align-items: center;
                    width: 100%;
                    padding: 14px 20px;
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                    border: none;
                    border-radius: var(--radius-md);
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    flex-direction: <?= $currentLangId == 2 ? 'row-reverse' : 'row' ?>;
                }

                .logout-btn:hover {
                    background: rgba(239, 68, 68, 0.2);
                    transform: translateY(-2px);
                }

                .logout-btn i {
                    <?= $currentLangId == 2 ? 'margin-left: 12px; margin-right: 0;' : 'margin-right: 12px; margin-left: 0;' ?>
                    font-size: 1.1rem;
                }

                /* Topbar - Modern Design with RTL support */
                .topbar {
                    position: fixed;
                    top: 0;
                    <?= $currentLangId == 2 ? 'right: 200px; left: 0;' : 'left: 200px; right: 0;' ?>
                    height: 70px;
                    background: var(--topbar-bg);
                    backdrop-filter: blur(20px);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0 30px;
                    z-index: 999;
                    border-bottom: 1px solid var(--glass-border);
                    transition: <?= $currentLangId == 2 ? 'right' : 'left' ?> 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .sidebar.collapsed ~ .topbar {
                    <?= $currentLangId == 2 ? 'right: 80px;' : 'left: 80px;' ?>
                }

                .topbar-left {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    order: <?= $currentLangId == 2 ? '2' : '1' ?>;
                }

                .topbar-right {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    order: <?= $currentLangId == 2 ? '1' : '2' ?>;
                }

                .toggle-btn {
                    width: 40px;
                    height: 40px;
                    border-radius: var(--radius-md);
                    background: var(--glass-bg);
                    border: 1px solid var(--glass-border);
                    color: var(--text-primary);
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    transition: all 0.3s ease;
                }

                .toggle-btn:hover {
                    background: var(--accent-hover);
                    transform: rotate(<?= $currentLangId == 2 ? '-90deg' : '90deg' ?>);
                    color: var(--accent-primary);
                }

                .theme-toggle {
                    width: 40px;
                    height: 40px;
                    border-radius: var(--radius-md);
                    background: var(--glass-bg);
                    border: 1px solid var(--glass-border);
                    color: var(--text-primary);
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    transition: all 0.3s ease;
                }

                .theme-toggle:hover {
                    background: var(--accent-hover);
                    transform: scale(1.1);
                }
                .settings {
                    width: 40px;
                    height: 40px;
                    border-radius: var(--radius-md);
                    background: var(--glass-bg);
                    border: 1px solid var(--glass-border);
                    color: var(--text-primary);
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    transition: all 0.3s ease;
                }
                .settings:hover {
                    background: var(--accent-hover);
                    transform: scale(1.1);
                }
                .user-info {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    flex-direction: <?= $currentLangId == 2 ? 'row-reverse' : 'row' ?>;
                }

                .user-avatar {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 600;
                    color: white;
                    font-size: 0.9rem;
                }

                .user-name {
                    font-weight: 600;
                    font-size: 0.95rem;
                }

                /* Main Content */
                .content {
                    margin-top: 70px;
                    <?= $currentLangId == 2 ? 'margin-right: 200px; margin-left: 0;' : 'margin-left: 200px; margin-right: 0;' ?>
                    padding: 30px;
                    min-height: calc(100vh - 70px);
                    transition: <?= $currentLangId == 2 ? 'margin-right' : 'margin-left' ?> 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .sidebar.collapsed ~ .content {
                    <?= $currentLangId == 2 ? 'margin-right: 80px;' : 'margin-left: 80px;' ?>
                }

                .content-frame {
                    width: 100%;
                    height: calc(100vh - 50px);
                    border: none;
                    border-radius: var(--radius-lg);
                    background: var(--card-bg);
                    box-shadow: var(--shadow);
                    backdrop-filter: blur(20px);
                    transition: all 0.3s ease;
                }

                /* Responsive Design */
                @media (max-width: 1024px) {
                    .sidebar {
                        transform: translateX(<?= $currentLangId == 2 ? '100%' : '-100%' ?>);
                    }
                    
                    .sidebar.active {
                        transform: translateX(0);
                    }
                    
                    .topbar {
                        <?= $currentLangId == 2 ? 'right: 0 !important;' : 'left: 0 !important;' ?>
                    }
                    
                    .content {
                        <?= $currentLangId == 2 ? 'margin-right: 0 !important;' : 'margin-left: 0 !important;' ?>
                    }
                    
                    .mobile-menu-btn {
                        display: flex !important;
                    }
                }

                @media (max-width: 768px) {
                    .content {
                        padding: 20px;
                    }
                    
                    .topbar {
                        padding: 0 20px;
                    }
                    
                    .user-name {
                        display: none;
                    }
                    
                    .toggle-btn {
                        display: none;
                    }
                }

                .mobile-menu-btn {
                    display: none;
                    width: 40px;
                    height: 40px;
                    border-radius: var(--radius-md);
                    background: var(--glass-bg);
                    border: 1px solid var(--glass-border);
                    color: var(--text-primary);
                    cursor: pointer;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    <?= $currentLangId == 2 ? 'margin-left: 10px; margin-right: 0;' : 'margin-right: 10px; margin-left: 0;' ?>
                }

                /* Loading Animation */
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }

                .loading {
                    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
                }

                /* Notification Badge */
                .notification-badge {
                    position: absolute;
                    top: 8px;
                    <?= $currentLangId == 2 ? 'left: 8px;' : 'right: 8px;' ?>
                    width: 8px;
                    height: 8px;
                    background: #ef4444;
                    border-radius: 50%;
                    border: 2px solid var(--sidebar-bg);
                }

                /* Floating Action Button */
                .fab {
                    position: fixed;
                    <?= $currentLangId == 2 ? 'left: 30px;' : 'right: 30px;' ?>
                    bottom: 30px;
                    width: 56px;
                    height: 56px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
                    color: white;
                    border: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                    cursor: pointer;
                    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
                    z-index: 100;
                    transition: all 0.3s ease;
                }

                .fab:hover {
                    transform: scale(1.1) rotate(90deg);
                    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
                }

                @media (max-width: 1024px) {
                    .toggle-btn {
                        display: none;
                    }
                }
                
                .sidebar-logo {
                    width: 100%;
                    max-width: 160px;
                    height: auto;
                    max-height: 70px;
                    object-fit: contain;
                }

                .sidebar.collapsed .sidebar-logo {
                    max-width: 60px;
                    max-height: 60px;
                }

                @media (max-width: 1024px) {
                    .sidebar-logo {
                        max-width: 140px;
                        max-height: 60px;
                    }
                }

                @media (max-width: 768px) {
                    .sidebar-logo {
                        max-width: 120px;
                        max-height: 55px;
                    }
                }
                
                #sidebarOverlay {
                    position: fixed;
                    inset: 0;
                    background: transparent;
                    z-index: 900;
                    display: none;
                }

                /* RTL specific adjustments */
                <?php if($currentLangId == 2): ?>
                .fa-chevron-left::before {
                    content: "\f054";
                }
                .fa-chevron-right::before {
                    content: "\f053";
                }
                <?php endif; ?>
</style>
</head>

<body class="<?php echo $theme === 1 ? 'light' : ''; ?>">

<!-- Sidebar -->
<div class="sidebar glass" id="sidebar">
    <div class="sidebar-header">
        <?php if (!empty($logoPath)): ?>
            <img 
                src="/College/<?php echo htmlspecialchars($logoPath); ?>" 
                alt="Logo"
                class="sidebar-logo"
            >
        <?php else: ?>
            <h2><?php echo htmlspecialchars($appName); ?></h2>
        <?php endif; ?>
    </div>
    
    <nav class="sidebar-nav">
        <a href="reports.php" target="contentFrame" class="nav-item active">
            <i class="fas fa-chart-pie"></i>
            <span><?= t('nav_dashboard') ?></span>
        </a>
        <a href="users/users.php" target="contentFrame" class="nav-item">
            <i class="fas fa-users"></i>
            <span><?= t('nav_users') ?></span>
        </a>
        <a href="subjects/subjects.php" target="contentFrame" class="nav-item">
            <i class="fas fa-book-open"></i>
            <span><?= t('nav_courses') ?></span>
        </a>
        <a href="classes/classes.php" target="contentFrame" class="nav-item">
            <i class="fas fa-chalkboard-teacher"></i>
            <span><?= t('nav_classes') ?></span>
        </a>
        <a href="teachers/teachers.php" target="contentFrame" class="nav-item">
            <i class="fas fa-user-tie"></i>
            <span><?= t('nav_teachers') ?></span>
        </a>
        <a href="students/students.php" target="contentFrame" class="nav-item">
            <i class="fas fa-user-graduate"></i>
            <span><?= t('nav_students') ?></span>
        </a>
       
        <?php if (($_SESSION['role'] ?? '') !== 'Finance'): ?>
            <a href="attendance/attendance.php" target="contentFrame" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span><?= t('nav_attendance') ?></span>
            </a>
        <?php endif; ?>

        <?php 
        $role = $_SESSION['role'] ?? '';
        if ($role === 'Finance' || $role === 'admin'): 
        ?>
            <a href="fees/fees.php" target="contentFrame" class="nav-item">
                <i class="fas fa-wallet"></i>
                <span><?= t('nav_finance') ?></span>
            </a>
            <a href="expenses/expenses.php" target="contentFrame" class="nav-item">
                <i class="fas fa-money-bill-transfer"></i>
                <span><?= t('nav_expenses') ?></span>
            </a> 
        <?php endif; ?>

        <?php if (($_SESSION['role'] ?? '') !== 'Finance'): ?>
            <a href="exams/exams.php" target="contentFrame" class="nav-item">
                <i class="fas fa-file-signature"></i>
                <span><?= t('nav_exams') ?></span>
            </a>
            
            <a href="messages/messages.php" target="contentFrame" class="nav-item">
                <i class="fas fa-comments"></i>
                <span><?= t('nav_messages') ?></span>
            </a>
        <?php endif; ?>
        
        <a href="alumni/alumni.php" target="contentFrame" class="nav-item">
            <i class="fas fa-user-graduate"></i>
            <span><?= t('nav_alumni') ?></span>
        </a>
       
        <?php if (($_SESSION['role'] ?? '') !== 'Finance'): ?>
            <a href="settings.php" target="contentFrame" class="nav-item">
                <i class="fas fa-cogs"></i>
                <span><?= t('nav_settings') ?></span>
            </a>
        <?php endif; ?>
        
    </nav>
    
    <div class="sidebar-footer">
        <button class="logout-btn" onclick="window.location.href='/College/config/logout.php'">
            <i class="fas fa-sign-out-alt"></i>
            <span><?= t('nav_logout') ?></span>
        </button>
    </div>
</div>
<div id="sidebarOverlay"></div>


<!-- Top Navbar -->
<div class="topbar glass">
    <div class="topbar-right">
        <button class="mobile-menu-btn" onclick="toggleMobileSidebar()" title="<?= t('mobile_menu') ?>">
            <i class="fas fa-bars"></i>
        </button>
        <div class="user-info">
            <div class="user-avatar">
                <?php 
                    $name = htmlspecialchars($_SESSION['name'] ?? 'Admin');
                    $initials = implode('', array_map(function($n) { 
                        return strtoupper(substr($n, 0, 1)); 
                    }, explode(' ', $name, 2)));
                    echo $initials;
                ?>
            </div>
            <span class="user-name"><?php echo $name; ?></span>
        </div>
    </div>
    
    <div class="topbar-left">
        <button class="toggle-btn" onclick="toggleSidebar()" title="<?= t('toggle_sidebar') ?>">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="theme-toggle" onclick="toggleTheme()" title="<?= t('toggle_theme') ?>">
            <i class="fas fa-moon"></i>
        </button>
       
        <?php
        function is_admin() {
            return ($_SESSION['role'] ?? '') === 'admin';
        }
        ?>
        
        <?php if (is_admin()): ?>
            <a href="settings.php" class="settings" title="<?= t('open_settings') ?>" target="contentFrame">
                <i class="fas fa-cog"></i>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content -->
<div class="content">
    <iframe name="contentFrame" src="reports.php" class="content-frame" id="contentFrame"></iframe>
</div>

<!-- Floating Action Button -->
<button class="fab" onclick="scrollToTop()" title="<?= t('scroll_to_top') ?>">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
        // ===============================
        // ELEMENTS
        // ===============================
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.querySelector('.toggle-btn i');
        const contentFrame = document.getElementById('contentFrame');
        const overlay = document.getElementById('sidebarOverlay');
        const isRTL = document.documentElement.dir === 'rtl';

        // ===============================
        // LOAD SIDEBAR COLLAPSE STATE
        // ===============================
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            if (isRTL) {
                toggleBtn.classList.remove('fa-chevron-left');
                toggleBtn.classList.add('fa-chevron-right');
            } else {
                toggleBtn.classList.remove('fa-chevron-left');
                toggleBtn.classList.add('fa-chevron-right');
            }
        }

        // ===============================
        // DESKTOP SIDEBAR TOGGLE
        // ===============================
        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');

            if (isRTL) {
                toggleBtn.classList.toggle('fa-chevron-left', !isCollapsed);
                toggleBtn.classList.toggle('fa-chevron-right', isCollapsed);
            } else {
                toggleBtn.classList.toggle('fa-chevron-left', !isCollapsed);
                toggleBtn.classList.toggle('fa-chevron-right', isCollapsed);
            }

            localStorage.setItem('sidebarCollapsed', isCollapsed);
            window.dispatchEvent(new Event('resize'));
        }

        // ===============================
        // MOBILE SIDEBAR TOGGLE
        // ===============================
        function toggleMobileSidebar() {
            sidebar.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                overlay.style.display = 'block';
            } else {
                overlay.style.display = 'none';
            }
        }

        // ===============================
        // CLOSE SIDEBAR ON OUTSIDE CLICK (MOBILE)
        // ===============================
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.style.display = 'none';
        });

        // ===============================
        // THEME TOGGLE
        // ===============================
        function toggleTheme() {
            const themeToggle = document.querySelector('.theme-toggle i');

            themeToggle.classList.toggle('fa-moon');
            themeToggle.classList.toggle('fa-sun');

            fetch('toggle_theme.php')
                .then(res => res.text())
                .then(mode => {
                    mode = parseInt(mode);
                    document.body.classList.toggle('light', mode === 1);

                    // Reload iframe to apply theme
                    contentFrame.contentWindow.location.reload();
                })
                .catch(() => {
                    document.body.classList.toggle('light');
                });
        }

        // ===============================
        // SCROLL TO TOP
        // ===============================
        function scrollToTop() {
            contentFrame.contentWindow.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ===============================
        // NAV ACTIVE STATE (FROM IFRAME)
        // ===============================
        window.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'navigation') {
                updateActiveNav(event.data.path);
            }
        });

        function updateActiveNav(path) {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            const navItem = document.querySelector(`[href*="${path}"]`);
            if (navItem) navItem.classList.add('active');
        }

        // ===============================
        // IFRAME LOADING STATES
        // ===============================
        contentFrame.addEventListener('load', () => {
            contentFrame.classList.remove('loading');
        });

        contentFrame.addEventListener('loadstart', () => {
            contentFrame.classList.add('loading');
        });

        // ===============================
        // INIT THEME ICON
        // ===============================
        document.addEventListener('DOMContentLoaded', () => {
            const isLight = document.body.classList.contains('light');
            const themeToggle = document.querySelector('.theme-toggle i');

            themeToggle.classList.toggle('fa-moon', !isLight);
            themeToggle.classList.toggle('fa-sun', isLight);
        });

        // ===============================
        // RESPONSIVE RESET
        // ===============================
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('active');
                overlay.style.display = 'none';
            }
        });

        // ===============================
        // RTL CHEVRON FIX
        // ===============================
        if (isRTL && sidebarCollapsed) {
            toggleBtn.classList.remove('fa-chevron-left');
            toggleBtn.classList.add('fa-chevron-right');
        }
</script>

</body>
</html>