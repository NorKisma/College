<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
if ($theme_res && $theme_row = $theme_res->fetch_assoc()) {
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}
/* =========================
   DELETE SLIDE
========================= */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Get image path
    $stmt = $conn->prepare("SELECT image_path FROM login_slideshow WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res) {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/College/' . $res['image_path'];
        if (file_exists($file)) unlink($file);

        $del = $conn->prepare("DELETE FROM login_slideshow WHERE id = ?");
        $del->bind_param("i", $id);
        $del->execute();
    }

    header("Location: delete_slide.php?deleted=1");
    exit;
}

/* =========================
   UPDATE SLIDE POSITIONS (AJAX)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reorder_slides'])) {
    $order = $_POST['order']; // array of slide IDs in new order

    foreach ($order as $pos => $id) {
        $stmt = $conn->prepare("UPDATE login_slideshow SET position = ? WHERE id = ?");
        $stmt->bind_param("ii", $pos, $id);
        $stmt->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Slides reordered successfully.']);
    exit;
}

/* =========================
   FETCH SLIDES
========================= */
$slides = $conn->query("
    SELECT id, image_path, position, is_active 
    FROM login_slideshow 
    ORDER BY position ASC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slideshow Management | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ==================== BASE & TYPOGRAPHY ==================== */
        :root {
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --success-500: #10b981;
            --success-600: #059669;
            --warning-500: #f59e0b;
            --warning-600: #d97706;
            --danger-500: #ef4444;
            --danger-600: #dc2626;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.light {
            --bg-primary: var(--gray-50);
            --bg-secondary: white;
            --bg-tertiary: var(--gray-100);
            --border-primary: var(--gray-200);
            --border-secondary: var(--gray-300);
            --text-primary: var(--gray-900);
            --text-secondary: var(--gray-600);
            --text-tertiary: var(--gray-500);
            --surface-card: white;
            --surface-hover: var(--gray-50);
            --surface-active: var(--gray-100);
        }

        body.dark {
            --bg-primary: var(--gray-900);
            --bg-secondary: var(--gray-800);
            --bg-tertiary: var(--gray-700);
            --border-primary: var(--gray-700);
            --border-secondary: var(--gray-600);
            --text-primary: var(--gray-50);
            --text-secondary: var(--gray-300);
            --text-tertiary: var(--gray-400);
            --surface-card: var(--gray-800);
            --surface-hover: var(--gray-700);
            --surface-active: var(--gray-600);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: background-color var(--transition-base), color var(--transition-base);
        }

        /* ==================== LAYOUT & CONTAINERS ==================== */
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* ==================== HEADER & NAVIGATION ==================== */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-primary);
        }

        .header-left h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.025em;
            margin-bottom: 0.25rem;
        }

        .header-left .subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        /* ==================== BREADCRUMB ==================== */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.875rem;
            color: var(--text-tertiary);
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '›';
            color: var(--text-tertiary);
        }

        .breadcrumb-item.active {
            color: var(--text-primary);
            font-weight: 500;
        }

        /* ==================== CARD COMPONENTS ==================== */
        .card {
            background: var(--surface-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-primary);
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition-base), border-color var(--transition-base);
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--border-secondary);
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-title i {
            color: var(--primary-500);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ==================== STATS & METRICS ==================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--surface-card);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border: 1px solid var(--border-primary);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .stat-icon.total { background: linear-gradient(135deg, var(--primary-500), var(--primary-600)); }
        .stat-icon.active { background: linear-gradient(135deg, var(--success-500), var(--success-600)); }
        .stat-icon.inactive { background: linear-gradient(135deg, var(--gray-500), var(--gray-600)); }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-change {
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }

        .stat-change.positive { color: var(--success-500); }
        .stat-change.negative { color: var(--danger-500); }

        /* ==================== TABLE COMPONENTS ==================== */
        .table-container {
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-primary);
            overflow: hidden;
            background: var(--surface-card);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
        }

        .table thead {
            background: var(--bg-tertiary);
        }

        .table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            border-bottom: 1px solid var(--border-primary);
            white-space: nowrap;
        }

        .table tbody tr {
            transition: background-color var(--transition-fast);
            border-bottom: 1px solid var(--border-primary);
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: var(--surface-hover);
        }

        .table td {
            padding: 1.25rem 1.5rem;
            color: var(--text-primary);
            vertical-align: middle;
        }

        /* ==================== SLIDE ITEM COMPONENTS ==================== */
        .slide-preview {
            width: 140px;
            height: 90px;
            border-radius: var(--radius-md);
            overflow: hidden;
            position: relative;
            border: 1px solid var(--border-primary);
            transition: transform var(--transition-base), box-shadow var(--transition-base);
        }

        .slide-preview:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .slide-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .slide-preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.5), transparent 50%);
            opacity: 0;
            transition: opacity var(--transition-fast);
            display: flex;
            align-items: flex-end;
            padding: 0.5rem;
        }

        .slide-preview:hover .slide-preview-overlay {
            opacity: 1;
        }

        .slide-info {
            color: white;
            font-size: 0.75rem;
        }

        /* ==================== STATUS BADGES ==================== */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
            width: fit-content;
        }

        .status-badge.active {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-600);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-badge.inactive {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-500);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* ==================== ACTION BUTTONS ==================== */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-icon {
            width: 2rem;
            height: 2rem;
            padding: 0;
            justify-content: center;
            border-radius: var(--radius-md);
        }

        .btn-primary {
            background: var(--primary-500);
            color: white;
            border-color: var(--primary-500);
        }

        .btn-primary:hover {
            background: var(--primary-600);
            border-color: var(--primary-600);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-500);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            background: var(--danger-500);
            color: white;
            border-color: var(--danger-500);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border-color: var(--border-primary);
        }

        .btn-ghost:hover {
            background: var(--surface-hover);
            color: var(--text-primary);
        }

        /* ==================== DRAG & DROP INDICATORS ==================== */
        .drag-handle {
            cursor: move;
            color: var(--text-tertiary);
            padding: 0.5rem;
            border-radius: var(--radius-sm);
            transition: color var(--transition-fast), background-color var(--transition-fast);
        }

        .drag-handle:hover {
            color: var(--primary-500);
            background-color: var(--surface-hover);
        }

        .sortable-ghost {
            opacity: 0.4;
            background-color: var(--surface-active);
        }

        .sortable-chosen {
            background-color: var(--surface-hover);
            box-shadow: var(--shadow-md);
        }

        .sortable-drag {
            transform: rotate(2deg);
            box-shadow: var(--shadow-xl);
        }

        /* ==================== MODAL & DIALOGS ==================== */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--surface-card);
            border-radius: var(--radius-lg);
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-1rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-tertiary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius-sm);
            transition: background-color var(--transition-fast);
        }

        .modal-close:hover {
            background-color: var(--surface-hover);
        }

        .modal-body {
            margin-bottom: 2rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* ==================== EMPTY STATE ==================== */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-tertiary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--text-tertiary);
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        /* ==================== TOAST NOTIFICATIONS ==================== */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            background: var(--surface-card);
            border: 1px solid var(--border-primary);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1001;
            transform: translateY(100%);
            opacity: 0;
            transition: transform var(--transition-base), opacity var(--transition-base);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast.success {
            border-left: 4px solid var(--success-500);
        }

        .toast.error {
            border-left: 4px solid var(--danger-500);
        }

        .toast-icon {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
        }

        .toast.success .toast-icon {
            background-color: var(--success-500);
        }

        .toast.error .toast-icon {
            background-color: var(--danger-500);
        }

        /* ==================== RESPONSIVE DESIGN ==================== */
        @media (max-width: 768px) {
            .admin-container {
                padding: 1rem;
            }

            .admin-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .card-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body class="<?= $theme === 1 ? 'light' : 'dark' ?>">
    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div class="toast-icon">
            <i class="fas fa-check"></i>
        </div>
        <div class="toast-content"></div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this slide? This action cannot be undone.</p>
                <p class="text-tertiary" style="margin-top: 0.5rem; font-size: 0.875rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    This will permanently remove the slide from the slideshow.
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">Delete Slide</button>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-left">
                <h1>Slideshow Management</h1>
                <p class="subtitle">Manage login page slideshow content and ordering</p>
            </div>
            <div class="header-actions">
                <a href="settings.php" class="btn btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                <a href="settings.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Upload New Slides
                </a>
            </div>
        </div>

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <div class="breadcrumb-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </div>
            <div class="breadcrumb-item">
                <i class="fas fa-sliders-h"></i>
                <span>Settings</span>
            </div>
            <div class="breadcrumb-item active">
                <i class="fas fa-images"></i>
                <span>Slideshow Management</span>
            </div>
        </div>

        <!-- Stats Overview -->
        <?php
        $total_slides = count($slides);
        $active_slides = array_filter($slides, fn($slide) => $slide['is_active']);
        $inactive_slides = $total_slides - count($active_slides);
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Slides</div>
                    <div class="stat-icon total">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
                <div class="stat-value"><?= $total_slides ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>Maximum: 10 slides</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Active Slides</div>
                    <div class="stat-icon active">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value"><?= count($active_slides) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-check"></i>
                    <span><?= count($active_slides) > 0 ? 'Ready for display' : 'No active slides' ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Inactive Slides</div>
                    <div class="stat-icon inactive">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                </div>
                <div class="stat-value"><?= $inactive_slides ?></div>
                <div class="stat-change <?= $inactive_slides > 0 ? 'negative' : 'positive' ?>">
                    <i class="fas fa-<?= $inactive_slides > 0 ? 'exclamation-triangle' : 'check' ?>"></i>
                    <span><?= $inactive_slides > 0 ? 'Review needed' : 'All slides active' ?></span>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-sliders-h"></i>
                    <span>Slideshow Slides</span>
                </div>
                <div class="header-actions">
                    <div class="btn btn-ghost" id="toggleStatusBtn">
                        <i class="fas fa-eye"></i>
                        <span>Toggle Status</span>
                    </div>
                    <div class="btn btn-ghost" onclick="showReorderInstructions()">
                        <i class="fas fa-info-circle"></i>
                        <span>Reorder Guide</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($slides)): ?>
                    <div class="empty-state">
                        <i class="fas fa-images"></i>
                        <h3>No Slides Available</h3>
                        <p>Upload slides to create a slideshow for the login page.</p>
                        <a href="settings.php" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-upload"></i>
                            Upload Slides
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table" id="slides-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="width: 60px;"></th>
                                    <th>Slide Preview</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="slides-tbody">
                                <?php foreach ($slides as $index => $slide): ?>
                                    <tr data-id="<?= $slide['id'] ?>" data-position="<?= $slide['position'] ?>">
                                        <td>
                                            <div class="drag-handle" title="Drag to reorder">
                                                <i class="fas fa-grip-vertical"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($slide['is_active']): ?>
                                                <i class="fas fa-check-circle" style="color: var(--success-500);"></i>
                                            <?php else: ?>
                                                <i class="fas fa-pause-circle" style="color: var(--danger-500);"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="slide-preview">
                                                <img src="/College/<?= htmlspecialchars($slide['image_path']) ?>" 
                                                     alt="Slide <?= $index + 1 ?>">
                                                <div class="slide-preview-overlay">
                                                    <div class="slide-info">
                                                        <i class="fas fa-expand"></i>
                                                        Preview
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="position-display">
                                                <span class="position-number"><?= $slide['position'] + 1 ?></span>
                                                <div class="position-controls" style="display: inline-flex; gap: 0.25rem; margin-left: 0.5rem;">
                                                    <!-- <button class="btn btn-sm btn-ghost" onclick="moveSlideUp(<?= $slide['id'] ?>)" 
                                                            title="Move up" <?= $slide['position'] === 0 ? 'disabled' : '' ?>>
                                                        <i class="fas fa-chevron-up"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-ghost" onclick="moveSlideDown(<?= $slide['id'] ?>)"
                                                            title="Move down" <?= $slide['position'] === $total_slides - 1 ? 'disabled' : '' ?>>
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button> -->
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="status-badge <?= $slide['is_active'] ? 'active' : 'inactive' ?>">
                                                <i class="fas fa-<?= $slide['is_active'] ? 'check-circle' : 'pause-circle' ?>"></i>
                                                <span><?= $slide['is_active'] ? 'Active' : 'Inactive' ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <!-- <button class="btn btn-sm btn-ghost" 
                                                        onclick="toggleSlideStatus(<?= $slide['id'] ?>, <?= $slide['is_active'] ?>)"
                                                        title="Toggle Status">
                                                    <i class="fas fa-<?= $slide['is_active'] ? 'pause' : 'play' ?>"></i>
                                                </button> -->
                                                <button class="btn btn-sm btn-ghost" 
                                                        onclick="previewSlide('<?= htmlspecialchars($slide['image_path']) ?>')"
                                                        title="Preview Full Size">
                                                    <i class="fas fa-expand"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="confirmDelete(<?= $slide['id'] ?>)"
                                                        title="Delete Slide">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-primary);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="color: var(--text-secondary); font-size: 0.875rem;">
                                <i class="fas fa-info-circle"></i>
                                Drag slides to reorder or use the arrow buttons. Changes are saved automatically.
                            </div>
                            <div>
                                <span class="btn btn-ghost" onclick="saveOrderManually()">
                                    <i class="fas fa-save"></i>
                                    Save Current Order
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include Sortable.js -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <script>
        // Toast notification system
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const content = toast.querySelector('.toast-content');
            const icon = toast.querySelector('.toast-icon i');
            
            content.textContent = message;
            toast.className = `toast ${type}`;
            
            if (type === 'success') {
                icon.className = 'fas fa-check';
                toast.style.borderLeftColor = 'var(--success-500)';
            } else {
                icon.className = 'fas fa-exclamation-triangle';
                toast.style.borderLeftColor = 'var(--danger-500)';
            }
            
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Modal functions
        let pendingDeleteId = null;

        function confirmDelete(slideId) {
            pendingDeleteId = slideId;
            document.getElementById('confirmModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.remove('active');
            pendingDeleteId = null;
        }

        function executeDelete() {
            if (pendingDeleteId) {
                window.location.href = `?delete=${pendingDeleteId}`;
            }
        }

        // Drag and drop initialization
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('slides-tbody');
            
            if (tbody) {
                const sortable = Sortable.create(tbody, {
                    animation: 200,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        // Update position numbers visually
                        updatePositionNumbers();
                        
                        // Send AJAX request to save new order
                        saveOrder();
                    }
                });
            }

            // Update position numbers visually
            function updatePositionNumbers() {
                const rows = tbody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    const positionCell = row.querySelector('.position-number');
                    if (positionCell) {
                        positionCell.textContent = index + 1;
                    }
                    
                    // Update arrow button states
                    const upBtn = row.querySelector('button[onclick*="moveSlideUp"]');
                    const downBtn = row.querySelector('button[onclick*="moveSlideDown"]');
                    
                    if (upBtn) {
                        upBtn.disabled = index === 0;
                        upBtn.style.opacity = index === 0 ? '0.5' : '1';
                    }
                    
                    if (downBtn) {
                        downBtn.disabled = index === rows.length - 1;
                        downBtn.style.opacity = index === rows.length - 1 ? '0.5' : '1';
                    }
                });
            }

            // Initial update
            updatePositionNumbers();
        });

        // Save order via AJAX
        function saveOrder() {
            const rows = document.querySelectorAll('#slides-tbody tr');
            const order = Array.from(rows).map(row => row.dataset.id);
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `reorder_slides=1&order[]=${order.join('&order[]=')}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Slides reordered successfully', 'success');
                    
                    // Update all position attributes
                    rows.forEach((row, index) => {
                        row.dataset.position = index;
                    });
                } else {
                    showToast('Error reordering slides', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Network error occurred', 'error');
            });
        }

        // Manual save trigger
        function saveOrderManually() {
            saveOrder();
        }

        // Move slide up/down functions
        function moveSlideUp(slideId) {
            const row = document.querySelector(`tr[data-id="${slideId}"]`);
            const prevRow = row.previousElementSibling;
            
            if (prevRow) {
                row.parentNode.insertBefore(row, prevRow);
                updatePositionNumbers();
                saveOrder();
            }
        }

        function moveSlideDown(slideId) {
            const row = document.querySelector(`tr[data-id="${slideId}"]`);
            const nextRow = row.nextElementSibling;
            
            if (nextRow) {
                row.parentNode.insertBefore(nextRow, row);
                updatePositionNumbers();
                saveOrder();
            }
        }

        // Toggle slide status
        function toggleSlideStatus(slideId, currentStatus) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `toggle_status=1&slide_id=${slideId}&current_status=${currentStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(`Slide ${currentStatus ? 'deactivated' : 'activated'}`, 'success');
                    // Reload page to reflect changes
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('Error updating slide status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Network error occurred', 'error');
            });
        }

        // Preview slide in new tab
        function previewSlide(imagePath) {
            const fullPath = `/College/${imagePath}`;
            window.open(fullPath, '_blank');
        }

        // Show reorder instructions
        function showReorderInstructions() {
            alert('Reorder Instructions:\n\n1. Drag the handle (⋮⋮) to move slides up or down\n2. Use arrow buttons for precise control\n3. Changes are saved automatically\n4. Maximum 10 slides allowed');
        }

        // Set up event listeners
        document.getElementById('confirmDeleteBtn').addEventListener('click', executeDelete);
        
        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
        
        // Show success message if deleted parameter exists
        <?php if (isset($_GET['deleted'])): ?>
            showToast('Slide deleted successfully', 'success');
            // Remove the query parameter from URL without reloading
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>
    </script>
</body>
</html>