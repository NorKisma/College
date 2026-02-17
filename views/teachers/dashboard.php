<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

/* ---------- TRANSLATION SYSTEM ---------- */
// Get user's language preference from teachers table
$user_id = $_SESSION['user_id'] ?? null;
$currentLangId = 1; // Default to English (id: 1)

if ($user_id) {
    $langQuery = $conn->prepare("SELECT language_id FROM teachers WHERE id = ?");
    $langQuery->bind_param("i", $user_id);
    $langQuery->execute();
    $result = $langQuery->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentLangId = $row['language_id'] ?? 1;
    }
}

// Function to get translation from database
function translate($key, $langId = null) {
    global $conn, $currentLangId;
    
    if ($langId === null) {
        $langId = $currentLangId;
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT translation_value FROM translations WHERE translation_key = ? AND language_id = ?");
    $stmt->bind_param("si", $key, $langId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['translation_value'];
    }
    
    // Fallback to English if translation not found
    if ($langId != 1) {
        $stmt = $conn->prepare("SELECT translation_value FROM translations WHERE translation_key = ? AND language_id = 1");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['translation_value'];
        }
    }
    
    // Return the key itself if no translation found
    return $key;
}

// Get all available languages for language switcher (optional)
$languages = [];
$langRes = $conn->query("SELECT * FROM languages WHERE is_active = 1 ORDER BY id ASC");
while ($row = $langRes->fetch_assoc()) {
    $languages[$row['id']] = $row['name'];
}

/* ---------- SAVE LANGUAGE PREFERENCE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_language'])) {
    $newLangId = (int)$_POST['language_id'];
    
    if ($user_id) {
        $updateStmt = $conn->prepare("UPDATE teachers SET language_id = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newLangId, $user_id);
        $updateStmt->execute();
        
        // Update current language
        $currentLangId = $newLangId;
        
        // Reload page to apply changes
        echo '<script>window.location.reload();</script>';
    }
}

/* ---------- GET THEME SETTINGS ---------- */
$theme_mode = 1; // default light
$themeRes = $conn->query("SELECT mode FROM theme LIMIT 1");
if($themeRes && $themeRes->num_rows > 0){
    $themeRow = $themeRes->fetch_assoc();
    $theme_mode = (int)$themeRow['mode'];
}
$isDark = ($theme_mode === 0);

// Set HTML language attribute
$langDir = [
    1 => ['lang' => 'en', 'dir' => 'ltr'],
    2 => ['lang' => 'ar', 'dir' => 'rtl'],
    3 => ['lang' => 'so', 'dir' => 'ltr']
];
$currentLang = $langDir[$currentLangId] ?? $langDir[1];

// Check if Arabic is selected
$isArabic = ($currentLangId == 2);
?>
<!DOCTYPE html>
<html lang="<?= $currentLang['lang'] ?>" dir="<?= $currentLang['dir'] ?>" data-bs-theme="<?php echo $isDark ? 'dark' : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= translate('page_title') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ====== RTL SUPPORT ====== */
        <?php if ($isArabic): ?>
        /* Move sidebar to RIGHT when Arabic is selected */
        .sidebar-wrapper {
            right: 0;
            left: auto;
        }
        
        .main-content {
            margin-right: 250px;
            margin-left: 0;
        }
        
        .sidebar-wrapper.collapsed ~ .main-content {
            margin-right: 70px;
        }
        
        .mobile-toggle {
            right: 20px;
            left: auto;
        }
        
        .sidebar-backdrop {
            right: 0;
        }
        
        /* For mobile - Arabic sidebar positioning */
        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                transform: translateX(100%);
            }
            
            .sidebar-wrapper.active {
                transform: translateX(0);
            }
        }
        <?php else: ?>
        /* Default LTR positioning */
        .sidebar-wrapper {
            left: 0;
        }
        
        .main-content {
            margin-left: 250px;
        }
        
        .sidebar-wrapper.collapsed ~ .main-content {
            margin-left: 70px;
        }
        
        .mobile-toggle {
            left: 20px;
        }
        
        .sidebar-backdrop {
            left: 0;
        }
        
        /* For mobile - LTR sidebar positioning */
        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                transform: translateX(-100%);
            }
            
            .sidebar-wrapper.active {
                transform: translateX(0);
            }
        }
        <?php endif; ?>
        
        /* ====== BASE STYLES ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            transition: all 0.3s ease;
            overflow-x: hidden;
        }
        
        /* ====== SIDEBAR ====== */
        .sidebar-wrapper {
            position: fixed;
            top: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--bs-dark);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-wrapper.collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-brand-icon {
            font-size: 24px;
            color: var(--bs-primary);
        }
        
        .sidebar-brand-text {
            font-size: 18px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .sidebar-wrapper.collapsed .sidebar-brand-text {
            display: none;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
        }
        
        .sidebar-toggle i {
            transition: transform 0.3s ease;
        }
        
        .sidebar-wrapper.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }
        
        /* ====== NAVIGATION ====== */
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .nav-item {
            list-style: none;
            margin-bottom: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left: 3px solid var(--bs-primary);
        }
        
        .nav-icon {
            width: 24px;
            text-align: center;
            font-size: 18px;
        }
        
        .nav-text {
            white-space: nowrap;
            overflow: hidden;
        }
        
        .sidebar-wrapper.collapsed .nav-text {
            display: none;
        }
        
        .sidebar-wrapper.collapsed .nav-link {
            justify-content: center;
            padding: 15px;
        }
        
        /* ====== LOGOUT BUTTON ====== */
        .logout-btn {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* ====== MAIN CONTENT ====== */
        .main-content {
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        /* ====== IFRAME CONTAINER ====== */
        .iframe-container {
            height: calc(100vh - 0px);
        }
        
        .iframe-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        
        /* ====== LOADING OVERLAY ====== */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: var(--bs-primary);
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* ====== MOBILE TOGGLE BUTTON ====== */
        .mobile-toggle {
            position: fixed;
            top: 20px;
            z-index: 999;
            background-color: var(--bs-primary);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .mobile-toggle.hidden {
            display: none;
        }
        
        @media (max-width: 991.98px) {
            .mobile-toggle {
                display: flex;
            }
            
            .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            
            .sidebar-wrapper.collapsed {
                width: 250px;
            }
            
            .sidebar-toggle {
                display: none;
            }
        }
        
        /* ====== SIDEBAR BACKDROP ====== */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        
        .sidebar-backdrop.active {
            display: block;
        }
        
        /* ====== LANGUAGE SWITCHER ====== */
        .language-switcher {
            position: fixed;
            top: 20px;
            <?php echo $isArabic ? 'left: 20px' : 'right: 20px'; ?>;
            z-index: 1000;
        }
        
        .language-dropdown {
            background-color: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding: 10px;
            min-width: 120px;
        }
        
        .language-option {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .language-option:hover {
            background-color: var(--bs-tertiary-bg);
        }
        
        .language-option.active {
            background-color: var(--bs-primary);
            color: white;
        }
    </style>
</head>
<body>

 

<!-- Mobile Menu Toggle -->
<button class="mobile-toggle" id="mobileToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Mobile Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Sidebar -->
<div class="sidebar-wrapper" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span class="sidebar-brand-text"><?= translate('dashboard') ?></span>
        </div>
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>
    
    <!-- Navigation Menu -->
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link active" onclick="loadPage('reports.php', this)">
                <div class="nav-icon">
                    <i class="fas fa-home"></i>
                </div>
                <span class="nav-text"><?= translate('dashboard') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" onclick="loadPage('classes.php', this)">
                <div class="nav-icon">
                    <i class="fas fa-book"></i>
                </div>
                <span class="nav-text"><?= translate('classes') ?></span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" onclick="loadPage('subjects_timetable.php', this)">
                <div class="nav-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="nav-text"><?= translate('subjects_timetable') ?></span>
            </a>
        </li>
         <li class="nav-item">
                <a class="nav-link" onclick="loadPage('exams/exams.php', this)">
                    <div class="nav-icon">
                        <i class="fas fa-file-pen"></i>
                    </div>
                    <span class="nav-text"><?= translate('exams') ?></span>
                </a>
            </li>
        <li class="nav-item">
            <a class="nav-link" onclick="loadPage('NOTES/NOTES.php', this)">
                <div class="nav-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <span class="nav-text"><?= translate('notes') ?></span>
            </a>
        </li>
        <li class="nav-item">
                <a class="nav-link" onclick="loadPage('messages/messages.php', this)">
                    <div class="nav-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <span class="nav-text"><?= translate('messages') ?></span>
                </a>
            </li>
        <li class="nav-item">
                <a class="nav-link" onclick="loadPage('PROFILE.php', this)">
                    <div class="nav-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="nav-text"><?= translate('profile') ?></span>
                </a>
            </li>
    </ul>
    
    <!-- Logout Button -->
    <div class="logout-btn">
        <li class="nav-item">
            <a class="nav-link" onclick="window.location='/../../../college/public/logout.php'">
                <div class="nav-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <span class="nav-text"><?= translate('logout') ?></span>
            </a>
        </li>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    
    <!-- Content Iframe -->
    <div class="iframe-container">
        <iframe src="reports.php" id="contentFrame" title="Dashboard Content"></iframe>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });
    
    // DOM Elements
    const sidebar = document.getElementById('sidebar');
    const contentFrame = document.getElementById('contentFrame');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Check if Arabic is selected
    const isArabic = <?php echo $isArabic ? 'true' : 'false'; ?>;
    
    // Initialize sidebar state
    let isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    let isMobile = window.innerWidth < 992;
    
    // Set initial state
    if (isSidebarCollapsed && !isMobile) {
        sidebar.classList.add('collapsed');
    }
    
    // Toggle sidebar function
    function toggleSidebar() {
        if (isMobile) {
            sidebar.classList.toggle('active');
            sidebarBackdrop.classList.toggle('active');
            mobileToggle.classList.toggle('hidden');
        } else {
            sidebar.classList.toggle('collapsed');
            isSidebarCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isSidebarCollapsed);
        }
    }
    
    mobileToggle.addEventListener('click', function () {
        toggleSidebar();
    });
    
    // Load page function
    function loadPage(page, element) {
        // Show loading overlay
        loadingOverlay.classList.add('active');
        
        // Update active navigation item
        navLinks.forEach(link => link.classList.remove('active'));
        element.classList.add('active');
        
        // Load content into iframe
        contentFrame.src = page;
        
        // On iframe load, hide loading overlay
        contentFrame.onload = function() {
            loadingOverlay.classList.remove('active');
            
            // Close sidebar on mobile after selection
            if (isMobile) {
                sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
                mobileToggle.classList.remove('hidden');
            }
        };
        
        // Handle iframe errors
        contentFrame.onerror = function() {
            loadingOverlay.classList.remove('active');
            alert('<?= translate('error_loading_page') ?>');
        };
    }
    
    // Close sidebar when clicking on backdrop (mobile)
    sidebarBackdrop.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarBackdrop.classList.remove('active');
        mobileToggle.classList.remove('hidden');
    });
    
    // Handle window resize
    function handleResize() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth < 992;
        
        // Reset sidebar state on breakpoint change
        if (wasMobile !== isMobile) {
            if (isMobile) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
                mobileToggle.classList.remove('hidden');
            } else {
                sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
                mobileToggle.classList.add('hidden');
                
                // Restore collapsed state on desktop
                if (isSidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                }
            }
        }
    }
    
    // Add event listeners
    window.addEventListener('resize', handleResize);
    
    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        handleResize();
        
        // Add smooth transition after load to prevent initial animation
        setTimeout(() => {
            document.body.style.transition = 'all 0.3s ease';
        }, 100);
    });
    
    // Handle iframe navigation internally
    contentFrame.addEventListener('load', function() {
        try {
            const iframeDoc = contentFrame.contentDocument || contentFrame.contentWindow.document;
            
            // Update parent page when iframe changes URL (if same origin)
            iframeDoc.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.target) {
                    e.preventDefault();
                    loadPage(link.href, document.querySelector('.nav-link.active'));
                }
            });
        } catch (error) {
            // Cross-origin iframe, can't access content
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + B to toggle sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            toggleSidebar();
        }
        
        // Escape to close sidebar on mobile
        if (e.key === 'Escape' && isMobile && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
            mobileToggle.classList.remove('hidden');
        }
    });
</script>

</body>
</html>