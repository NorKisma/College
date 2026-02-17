<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php'; 

// READ THEME FROM DATABASE
$themeQ = $conn->query("SELECT mode FROM theme LIMIT 1");
$themeRow = $themeQ->fetch_assoc();
$theme = ($themeRow && $themeRow['mode'] == 0) ? 'dark' : 'light';

$id = (int)$_GET['id'];

// Fetch parent
$parent = $conn->query("SELECT * FROM parents WHERE id=$id")->fetch_assoc();

// Fetch all students linked by name OR phone
$students = $conn->query("
    SELECT * FROM students
    WHERE guardian_phone = '{$parent['phone']}'
       OR guardian_name  = '{$parent['name']}'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($parent['name']); ?> | Parent Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #7209b7;
            --success-color: #06d6a0;
            --info-color: #118ab2;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --light-bg: #f8fafc;
            --dark-bg: #1e293b;
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body.light {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: #334155;
            min-height: 100vh;
        }
        
        body.dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        
        .container-fluid {
            max-width: 1400px;
            padding: 30px;
        }
        
        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        
        .header-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        .header-avatar i {
            font-size: 2.5rem;
        }
        
        /* Card Styles */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
        }
        
        body.dark .glass-card {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(114, 9, 183, 0.1));
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 25px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        body.dark .card-header {
            background: rgba(67, 97, 238, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }
        
        /* Button Styles */
        .btn-back {
            background: linear-gradient(135deg, #64748b, #475569);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(100, 116, 139, 0.4);
            color: white;
        }
        
        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 25px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        body.dark .info-label {
            color: #94a3b8;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #1e293b;
            word-break: break-word;
        }
        
        body.dark .info-value {
            color: #f1f5f9;
        }
        
        /* Table Styles */
        .table-container {
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .table {
            margin: 0;
        }
        
        .table thead th {
            background: rgba(248, 250, 252, 0.9);
            color: #475569;
            font-weight: 600;
            padding: 18px 20px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        body.dark .table thead th {
            background: rgba(30, 41, 59, 0.9);
            color: #cbd5e1;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        body.dark .table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
        }
        
        body.dark .table tbody tr:hover {
            background: rgba(67, 97, 238, 0.15);
        }
        
        .table tbody td {
            padding: 18px 20px;
            vertical-align: middle;
        }
        
        /* Badge Styles */
        .badge-gender {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-male {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            border: 1px solid rgba(67, 97, 238, 0.3);
        }
        
        .badge-female {
            background: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 71, 111, 0.3);
        }
        
        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #94a3b8;
        }
        
        body.dark .empty-state {
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #cbd5e1;
        }
        
        body.dark .empty-state i {
            color: #475569;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
            text-decoration: none;
        }
        
        .action-btn-primary {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            border: 1px solid rgba(67, 97, 238, 0.3);
        }
        
        .action-btn-primary:hover {
            background: rgba(67, 97, 238, 0.2);
            transform: translateY(-2px);
        }
        
        body.dark .action-btn-primary {
            background: rgba(67, 97, 238, 0.2);
            color: #e2e8f0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .main-header {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            
            .table-responsive {
                border-radius: var(--border-radius);
            }
            
            .quick-actions {
                justify-content: center;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        body.dark ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        body.dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        body.dark ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>

<body class="<?= $theme ?>">

<div class="container-fluid p-4 fade-in">
    <!-- Back Button -->
    <a href="parents.php" class="btn-back fade-in">
        <i class="bi bi-arrow-left"></i>
        Back to Parents List
    </a>

    <!-- Main Header -->
    <div class="main-header fade-in">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center">
            <div class="header-avatar me-md-4">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="flex-grow-1">
                <h1 class="mb-2">
                    <?php echo htmlspecialchars($parent['name']); ?>
                    <span class="badge bg-light text-dark ms-2 fs-6">Parent</span>
                </h1>
                <p class="mb-0 opacity-75">
                    <i class="bi bi-telephone me-1"></i> <?php echo htmlspecialchars($parent['phone']); ?>
                    <?php if($parent['email']): ?>
                        <span class="ms-3">
                            <i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($parent['email']); ?>
                        </span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-light text-dark px-3 py-2">
                    <i class="bi bi-people me-1"></i>
                    <?php echo $students->num_rows; ?> Children
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions mb-4 fade-in">
        <a href="#" class="action-btn action-btn-primary">
            <i class="bi bi-telephone"></i> Call Parent
        </a>
        <a href="mailto:<?php echo htmlspecialchars($parent['email']); ?>" class="action-btn action-btn-primary">
            <i class="bi bi-envelope"></i> Send Email
        </a>
        <a href="#" class="action-btn action-btn-primary">
            <i class="bi bi-chat"></i> Send Message
        </a>
        <a href="#" class="action-btn action-btn-primary">
            <i class="bi bi-printer"></i> Print Profile
        </a>
    </div>

    <!-- Parent Details Card -->
    <div class="glass-card mb-4 fade-in" style="animation-delay: 0.1s;">
        <div class="card-header d-flex align-items-center">
            <i class="bi bi-person-lines-fill me-2"></i>
            Parent Details
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">
                    <i class="bi bi-person me-1"></i> Full Name
                </span>
                <span class="info-value"><?php echo htmlspecialchars($parent['name']); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">
                    <i class="bi bi-telephone me-1"></i> Phone Number
                </span>
                <span class="info-value">
                    <a href="tel:<?php echo htmlspecialchars($parent['phone']); ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($parent['phone']); ?>
                    </a>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">
                    <i class="bi bi-envelope me-1"></i> Email Address
                </span>
                <span class="info-value">
                    <?php if($parent['email']): ?>
                        <a href="mailto:<?php echo htmlspecialchars($parent['email']); ?>" class="text-decoration-none">
                            <?php echo htmlspecialchars($parent['email']); ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Not provided</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">
                    <i class="bi bi-briefcase me-1"></i> Occupation
                </span>
                <span class="info-value">
                    <?php echo $parent['occupation'] ? htmlspecialchars($parent['occupation']) : '<span class="text-muted">Not specified</span>'; ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">
                    <i class="bi bi-geo-alt me-1"></i> Address
                </span>
                <span class="info-value">
                    <?php echo $parent['address'] ? htmlspecialchars($parent['address']) : '<span class="text-muted">Not provided</span>'; ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">
                    <i class="bi bi-calendar me-1"></i> Registration Date
                </span>
                <span class="info-value">
                    <?php 
                    $regDate = isset($parent['created_at']) ? $parent['created_at'] : date('Y-m-d');
                    echo date('F d, Y', strtotime($regDate));
                    ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Children Card -->
    <div class="glass-card fade-in" style="animation-delay: 0.2s;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-people-fill me-2"></i>
                Children in School
                <span class="badge bg-primary ms-2"><?php echo $students->num_rows; ?></span>
            </div>
            <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print List
            </button>
        </div>
        
        <?php if($students->num_rows == 0): ?>
            <div class="empty-state">
                <i class="bi bi-people display-4"></i>
                <h4 class="mt-3 mb-2">No Children Found</h4>
                <p class="text-muted">No students are currently linked to this parent.</p>
                <a href="#" class="btn btn-primary mt-2">
                    <i class="bi bi-link me-1"></i> Link Student
                </a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                                <th>Admission Date</th>
                                <th>Grade/Class</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1; 
                            while($s = $students->fetch_assoc()): 
                                $genderClass = strtolower($s['gender']) == 'male' ? 'badge-male' : 'badge-female';
                            ?>
                                <tr>
                                    <td class="fw-bold"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                                <i class="bi bi-person-circle text-primary"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($s['name']); ?></strong>
                                                <?php if(isset($s['student_id']) && $s['student_id']): ?>
                                                    <div class="text-muted small">ID: <?php echo htmlspecialchars($s['student_id']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-gender <?php echo $genderClass; ?>">
                                            <?php echo htmlspecialchars($s['gender']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $dob = $s['dob'] ? date('M d, Y', strtotime($s['dob'])) : 'N/A';
                                        echo $dob;
                                        ?>
                                        <?php if($s['dob']): ?>
                                            <div class="text-muted small">
                                                Age: <?php echo date_diff(date_create($s['dob']), date_create('today'))->y; ?> years
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $admissionDate = $s['admission_date'] ? date('M d, Y', strtotime($s['admission_date'])) : 'N/A';
                                        echo $admissionDate;
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        // Assuming there's a class field or related table
                                        $classInfo = isset($s['class_name']) ? $s['class_name'] : 'N/A';
                                        echo htmlspecialchars($classInfo);
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Summary Footer -->
            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing <?php echo $students->num_rows; ?> children linked to this parent
                </div>
                <div>
                     
                  
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Export children list function
    function exportChildrenList() {
        alert('Export functionality would be implemented here.\nThis would generate a PDF or CSV file of the children list.');
        // In a real implementation, this would make an AJAX call to generate an export
    }
    
    // Print functionality enhancement
    window.addEventListener('beforeprint', function() {
        document.querySelector('.btn-back').style.display = 'none';
        document.querySelector('.quick-actions').style.display = 'none';
    });
    
    window.addEventListener('afterprint', function() {
        document.querySelector('.btn-back').style.display = 'inline-flex';
        document.querySelector('.quick-actions').style.display = 'flex';
    });
    
    // Add hover effects to table rows
    document.addEventListener('DOMContentLoaded', function() {
        const tableRows = document.querySelectorAll('.table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
</body>
</html>