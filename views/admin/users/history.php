<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

// Check authentication
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    header('Location: login.php');
    exit;
}
date_default_timezone_set('Africa/Mogadishu'); // or your local timezone
 


// Check admin permissions (optional - add if needed)
// if ($_SESSION['role'] !== 'admin') {
//     header('Location: ../dashboard.php');
//     exit;
// }

// Pagination setup
$records_per_page = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filtering
$filter_action = $_GET['action'] ?? '';
$filter_user = $_GET['user_id'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Build query with filters
$query = "SELECT al.*, u.username, u.email, u.role 
          FROM audit_logs al 
          LEFT JOIN users u ON al.user_id = u.id 
          WHERE 1=1";
$params = [];

if (!empty($filter_action)) {
    $query .= " AND al.action LIKE ?";
    $params[] = "%$filter_action%";
}

if (!empty($filter_user)) {
    $query .= " AND al.user_id = ?";
    $params[] = $filter_user;
}

if (!empty($start_date)) {
    $query .= " AND DATE(al.created_at) >= ?";
    $params[] = $start_date;
}

if (!empty($end_date)) {
    $query .= " AND DATE(al.created_at) <= ?";
    $params[] = $end_date;
}

// Get total count for pagination
$query = "
    SELECT 
        al.*, 
        u.username, 
        u.email, 
        r.name AS role
    FROM audit_logs al
    LEFT JOIN users u ON al.user_id = u.id
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE 1=1
";

$count_query = "
    SELECT COUNT(*) AS total
    FROM audit_logs al
    LEFT JOIN users u ON al.user_id = u.id
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE 1=1
";

$stmt = $conn->prepare($count_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error . "<br>Query: " . $count_query);
}


if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_records = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get audit logs with pagination
$query .= " ORDER BY al.created_at DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;

$stmt = $conn->prepare($query);
if ($params) {
    $types = str_repeat('s', count($params) - 2) . 'ii';
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$audit_logs = $result->fetch_all(MYSQLI_ASSOC);

// Get unique users for filter dropdown (optional)
$user_query = "
    SELECT 
        u.id, 
        u.username, 
        u.email, 
        r.name AS role
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    ORDER BY u.username
";
$user_result = $conn->query($user_query);
$users = $user_result->fetch_all(MYSQLI_ASSOC);


// Function to get icon based on action
function getActionIcon($action) {
    $action_lower = strtolower($action);

    // Admin actions
    if (strpos($action_lower, 'admin') !== false) {
        if (strpos($action_lower, 'create') !== false) return '<i class="fas fa-user-shield text-primary"></i>';
        if (strpos($action_lower, 'edit') !== false) return '<i class="fas fa-user-shield text-warning"></i>';
        if (strpos($action_lower, 'delete') !== false) return '<i class="fas fa-user-shield text-danger"></i>';
        return '<i class="fas fa-user-shield text-primary"></i>';
    }

    // Student actions
    if (strpos($action_lower, 'student') !== false) {
        if (strpos($action_lower, 'create') !== false) return '<i class="fas fa-user-graduate text-success"></i>';
        if (strpos($action_lower, 'edit') !== false) return '<i class="fas fa-user-graduate text-warning"></i>';
        if (strpos($action_lower, 'delete') !== false) return '<i class="fas fa-user-graduate text-danger"></i>';
        return '<i class="fas fa-user-graduate text-success"></i>';
    }

    // Parent actions
    if (strpos($action_lower, 'parent') !== false) {
        if (strpos($action_lower, 'create') !== false) return '<i class="fas fa-user-friends text-success"></i>';
        if (strpos($action_lower, 'edit') !== false) return '<i class="fas fa-user-friends text-warning"></i>';
        if (strpos($action_lower, 'delete') !== false) return '<i class="fas fa-user-friends text-danger"></i>';
        return '<i class="fas fa-user-friends text-info"></i>';
    }

    // Login/logout
    if (strpos($action_lower, 'login') !== false) return '<i class="fas fa-key text-info"></i>';
    if (strpos($action_lower, 'logout') !== false) return '<i class="fas fa-right-from-bracket text-secondary"></i>';

    // CRUD generic
    if (strpos($action_lower, 'create') !== false || strpos($action_lower, 'insert') !== false) return '<i class="fas fa-circle-plus text-success"></i>';
    if (strpos($action_lower, 'edit') !== false || strpos($action_lower, 'update') !== false) return '<i class="fas fa-pen-to-square text-warning"></i>';
    if (strpos($action_lower, 'delete') !== false || strpos($action_lower, 'remove') !== false) return '<i class="fas fa-trash-can text-danger"></i>';
    if (strpos($action_lower, 'view') !== false || strpos($action_lower, 'read') !== false) return '<i class="fas fa-eye text-primary"></i>';

    // Transactions
    if (strpos($action_lower, 'transaction') !== false || strpos($action_lower, 'payment') !== false) return '<i class="fas fa-money-bill-wave text-success"></i>';
    if (strpos($action_lower, 'enroll') !== false) return '<i class="fas fa-clipboard-list text-info"></i>';
    if (strpos($action_lower, 'grade') !== false) return '<i class="fas fa-chart-bar text-info"></i>';
    if (strpos($action_lower, 'attendance') !== false) return '<i class="fas fa-calendar-alt text-info"></i>';

    // System
    if (strpos($action_lower, 'export') !== false) return '<i class="fas fa-file-export text-primary"></i>';
    if (strpos($action_lower, 'import') !== false) return '<i class="fas fa-file-import text-primary"></i>';
    if (strpos($action_lower, 'backup') !== false) return '<i class="fas fa-database text-primary"></i>';
    if (strpos($action_lower, 'reset') !== false) return '<i class="fas fa-sync text-primary"></i>';

    return '<i class="fas fa-clipboard-list text-dark"></i>'; // default
}
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme_row = $theme_res->fetch_assoc();
$theme = (int)($theme_row['mode'] ?? 0);


function time_ago($datetime)
{
    if (empty($datetime)) return '—';

    $timestamp = strtotime($datetime);
    if (!$timestamp) return '—';

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return $diff . ' sec ago';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' mins ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } elseif ($diff < 2592000) {
        return floor($diff / 86400) . ' days ago';
    } elseif ($diff < 31536000) {
        return floor($diff / 2592000) . ' months ago';
    } else {
        return floor($diff / 31536000) . ' years ago';
    }
}


// Function to get color class based on action type
function getActionColor($action) {
    $action_lower = strtolower($action);
    
    if (strpos($action_lower, 'create') !== false) return 'text-success';
    if (strpos($action_lower, 'edit') !== false || strpos($action_lower, 'update') !== false) return 'text-warning';
    if (strpos($action_lower, 'delete') !== false) return 'text-danger';
    if (strpos($action_lower, 'login') !== false) return 'text-info';
    if (strpos($action_lower, 'logout') !== false) return 'text-secondary';
    
    return 'text-primary';
}

// Function to format description for better readability
function formatDescription($description) {
    if (empty($description)) return '-';
    
    // Add line breaks for long descriptions
    $desc = htmlspecialchars($description);
    $desc = preg_replace('/(ID:|User:|From:|To:)/', '<br><strong>$1</strong>', $desc);
    $desc = preg_replace('/\n/', '<br>', $desc);
    
    return $desc;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - System Transactions</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .audit-icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        .action-cell {
            min-width: 200px;
        }
        .user-cell {
            min-width: 150px;
        }
        .description-cell {
            max-width: 400px;
            word-wrap: break-word;
        }
        .badge-admin { background-color: #6f42c1; }
        .badge-teacher { background-color: #20c997; }
        .badge-student { background-color: #0dcaf0; }
        .badge-parent { background-color: #fd7e14; }
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .filter-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,.025);
        }
        .time-ago {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body class="<?= $theme === 0 ? 'dark' : '' ?>">
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-history me-2"></i>Audit Logs & Transactions</h1>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="exportLogs()">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-card">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="action" class="form-label">Action Type</label>
                            <input type="text" class="form-control" id="action" name="action" 
                                   placeholder="e.g., create, edit, delete" value="<?php echo htmlspecialchars($filter_action); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-control" id="user_id" name="user_id">
                                <option value="">All Users</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" 
                                        <?php echo ($filter_user == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['username'] . ' (' . $user['role'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="?" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-users"></i> Total Logs</h5>
                                <h2 class="card-text"><?php echo $total_records; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-plus-circle"></i> Create Actions</h5>
                                <h2 class="card-text">
                                    <?php
                                    $create_count = array_reduce($audit_logs, function($carry, $log) {
                                        return $carry + (stripos($log['action'], 'create') !== false ? 1 : 0);
                                    }, 0);
                                    echo $create_count;
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-edit"></i> Edit Actions</h5>
                                <h2 class="card-text">
                                    <?php
                                    $edit_count = array_reduce($audit_logs, function($carry, $log) {
                                        return $carry + (stripos($log['action'], 'edit') !== false || stripos($log['action'], 'update') !== false ? 1 : 0);
                                    }, 0);
                                    echo $edit_count;
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-trash"></i> Delete Actions</h5>
                                <h2 class="card-text">
                                    <?php
                                    $delete_count = array_reduce($audit_logs, function($carry, $log) {
                                        return $carry + (stripos($log['action'], 'delete') !== false ? 1 : 0);
                                    }, 0);
                                    echo $delete_count;
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit Logs Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Transaction History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($audit_logs)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No audit logs found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th width="80">Icon</th>
                                            <th class="action-cell">Action</th>
                                            <th class="user-cell">Performed By</th>
                                            <th>Description</th>
                                            <th width="180">Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($audit_logs as $index => $log): ?>
                                            <?php
                                            $icon = getActionIcon($log['action'], $log['description']);
                                            $color_class = getActionColor($log['action']);
                                            $formatted_desc = formatDescription($log['description']);
                                            $time_ago = time_ago($log['created_at']); // Assuming you have a time_ago helper function
                                            ?>
                                            <tr>
                                                <td><?php echo $offset + $index + 1; ?></td>
                                                <td class="audit-icon"><?php echo $icon; ?></td>
                                                <td>
                                                    <span class="<?php echo $color_class; ?> fw-bold">
                                                        <?php echo htmlspecialchars($log['action']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($log['username']): ?>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <i class="fas fa-user-circle"></i>
                                                            </div>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                                                                <br>
                                                                <span class="badge badge-<?php echo strtolower($log['role'] ?? 'unknown'); ?>">
                                                                    <?php echo htmlspecialchars($log['role'] ?? 'Unknown'); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">User not found</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="description-cell">
                                                    <small><?php echo $formatted_desc; ?></small>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?>
                                                    <br>
                                                    <small class="time-ago">
                                                        <i class="far fa-clock"></i> <?php echo $time_ago; ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <div class="pagination-container">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['action']) ? '&action=' . htmlspecialchars($_GET['action']) : ''; ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['action']) ? '&action=' . htmlspecialchars($_GET['action']) : ''; ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['action']) ? '&action=' . htmlspecialchars($_GET['action']) : ''; ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                                <div class="text-center text-muted">
                                    Showing <?php echo min(($offset + 1), $total_records); ?> to 
                                    <?php echo min(($offset + $records_per_page), $total_records); ?> of 
                                    <?php echo $total_records; ?> entries
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Legend -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-key me-2"></i>Icon Legend</h6>
                    </div>
                   <div class="card-body">
    <div class="row">
        <div class="col-md-3">
            <p class="text-primary"><i class="fas fa-user-shield me-2"></i> Admin Management</p>
            <p class="text-success"><i class="fas fa-user-graduate me-2"></i> Student Management</p>
            <p class="text-info"><i class="fas fa-user-friends me-2"></i> Parent Management</p>
        </div>
        <div class="col-md-3">
            <p class="text-success"><i class="fas fa-circle-plus me-2"></i> Create/Insert</p>
            <p class="text-warning"><i class="fas fa-pen-to-square me-2"></i> Edit/Update</p>
            <p class="text-danger"><i class="fas fa-trash-can me-2"></i> Delete/Remove</p>
        </div>
        <div class="col-md-3">
            <p class="text-info"><i class="fas fa-key me-2"></i> Login</p>
            <p class="text-secondary"><i class="fas fa-right-from-bracket me-2"></i> Logout</p>
            <p class="text-success"><i class="fas fa-money-bill-wave me-2"></i> Transactions</p>
        </div>
        <div class="col-md-3">
            <p class="text-primary"><i class="fas fa-file-export me-2"></i> Export</p>
            <p class="text-primary"><i class="fas fa-file-import me-2"></i> Import</p>
            <p class="text-dark"><i class="fas fa-clipboard-list me-2"></i> Other Actions</p>
        </div>
    </div>
</div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportLogs() {
            // Create export parameters
            let params = new URLSearchParams();
            
            // Add current filter parameters
            <?php if (!empty($filter_action)): ?>params.append('action', '<?php echo $filter_action; ?>');<?php endif; ?>
            <?php if (!empty($filter_user)): ?>params.append('user_id', '<?php echo $filter_user; ?>');<?php endif; ?>
            <?php if (!empty($start_date)): ?>params.append('start_date', '<?php echo $start_date; ?>');<?php endif; ?>
            <?php if (!empty($end_date)): ?>params.append('end_date', '<?php echo $end_date; ?>');<?php endif; ?>
            
            // Redirect to export script
            window.location.href = 'export_audit_logs.php?' + params.toString();
        }
        
        // Auto-refresh every 5 minutes (optional)
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes in milliseconds
        
        // Tooltip initialization
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>