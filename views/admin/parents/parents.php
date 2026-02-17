<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php'; 

// READ THEME FROM DATABASE
$themeQ = $conn->query("SELECT mode FROM theme LIMIT 1");
$themeRow = $themeQ->fetch_assoc();
$theme = ($themeRow && $themeRow['mode'] == 0) ? 'dark' : 'light';

// SEARCH
$search = $_GET['search'] ?? '';
$where = "";
if($search){
    $safe = $conn->real_escape_string($search);
    $where = "WHERE name LIKE '%$safe%' OR phone LIKE '%$safe%'";
}


/*
  Count students by:
  guardian_phone == parent.phone OR
  guardian_name  == parent.name
*/
$parents = $conn->query("
    SELECT 
        p.*,
        (
            SELECT COUNT(*) 
            FROM students s 
            WHERE s.guardian_phone = p.phone 
               OR s.guardian_name = p.name
        ) AS total_students
    FROM parents p
    $where
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="en" class="<?= $theme == 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parents Management | Academic Portal</title>
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --primary-dark: #3730a3;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #0ea5e9;
            --dark-bg: #0f172a;
            --dark-surface: #1e293b;
            --light-bg: #f8fafc;
            --light-surface: #ffffff;
        }
        
        * {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 5px rgba(79, 70, 229, 0.5); }
            50% { box-shadow: 0 0 15px rgba(79, 70, 229, 0.8); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .animate-slide-in {
            animation: slideIn 0.4s ease-out;
        }
        
        .animate-pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite;
        }
        
        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glass-dark {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(79, 70, 229, 0.5);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(79, 70, 229, 0.7);
        }
        
        /* Smooth transitions */
        .smooth-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Card hover effects */
        .hover-card {
            transition: all 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Input focus effects */
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
            border-color: var(--primary);
        }
        
        /* Table row hover effect */
        .table-row-hover:hover {
            background-color: rgba(79, 70, 229, 0.05);
        }
        
        /* Modal animations */
        .modal-enter {
            animation: modalFadeIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        /* Status badge animations */
        .badge-pulse {
            position: relative;
        }
        
        .badge-pulse::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 9999px;
            background: currentColor;
            opacity: 0.3;
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        
        @keyframes ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }
    </style>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#4f46e5',
                            600: '#4338ca',
                            700: '#3730a3',
                        },
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                        },
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'spin-slow': 'spin 3s linear infinite',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <header class="mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 shadow-lg animate-pulse-glow">
                            <i class="fas fa-user-friends text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Parents Management</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage parent information and their associated students</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <a href="add_parents.php" class="group flex items-center gap-2 bg-gradient-to-r from-primary-500 to-primary-700 hover:from-primary-600 hover:to-primary-800 text-white font-medium px-5 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-user-plus"></i>
                        <span>Add New Parent</span>
                    </a>
                    <a href="../users/users.php" class="group flex items-center gap-2 bg-gradient-to-r from-gray-700 to-gray-800 dark:from-gray-800 dark:to-gray-900 hover:from-gray-800 hover:to-gray-900 dark:hover:from-gray-700 dark:hover:to-gray-800 text-white font-medium px-5 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Users</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8 animate-fade-in" style="animation-delay: 0.1s">
            <?php
            $totalParents = $conn->query("SELECT COUNT(*) as total FROM parents")->fetch_assoc()['total'];
            $totalStudentsLinked = $conn->query("SELECT COUNT(DISTINCT s.id) as linked FROM students s INNER JOIN parents p ON s.guardian_phone = p.phone OR s.guardian_name = p.name")->fetch_assoc()['linked'];
            $totalStudents = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
            $percentageLinked = $totalStudents > 0 ? round(($totalStudentsLinked/$totalStudents)*100, 1) : 0;
            ?>
            
            <div class="glass dark:glass-dark p-5 rounded-2xl shadow-sm hover-card border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Parents</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $totalParents ?></h3>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-database mr-1"></i> All registered parents</p>
                </div>
            </div>
            
            <div class="glass dark:glass-dark p-5 rounded-2xl shadow-sm hover-card border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Linked Students</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $totalStudentsLinked ?></h3>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <i class="fas fa-link text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= $percentageLinked ?>% of all students</p>
                </div>
            </div>
            
            <div class="glass dark:glass-dark p-5 rounded-2xl shadow-sm hover-card border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Students per Parent</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            <?= $totalParents > 0 ? round($totalStudentsLinked/$totalParents, 1) : 0 ?>
                        </h3>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                        <i class="fas fa-chart-pie text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Average linkage ratio</p>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="glass dark:glass-dark rounded-2xl p-5 mb-8 shadow-sm border border-gray-200 dark:border-gray-800 animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex flex-col lg:flex-row gap-5">
                <!-- Search Bar -->
                <div class="flex-grow">
                    <form method="GET" class="relative">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input
                                name="search"
                                value="<?= htmlspecialchars($search) ?>"
                                placeholder="Search parents by name or phone number..."
                                class="input-focus w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none"
                            />
                            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-4 py-1.5 rounded-lg transition-colors shadow-md">
                                <i class="fas fa-search mr-1"></i>
                                Search
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Additional Actions -->
                <div class="flex items-center gap-3">
                    <!-- <button onclick="exportParents()" class="group flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white px-5 py-3 rounded-xl transition-colors">
                        <i class="fas fa-file-export group-hover:animate-spin-slow"></i>
                        <span>Export</span>
                    </button>
                     -->
                    <?php if($search): ?>
                        <a href="parents.php" class="flex items-center gap-2 bg-gradient-to-r from-gray-300 to-gray-400 dark:from-gray-700 dark:to-gray-800 hover:from-gray-400 hover:to-gray-500 dark:hover:from-gray-600 dark:hover:to-gray-700 text-gray-800 dark:text-white px-5 py-3 rounded-xl transition-colors">
                            <i class="fas fa-times"></i>
                            <span>Clear Search</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Parents Table -->
        <div class="glass dark:glass-dark rounded-2xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-800 animate-fade-in" style="animation-delay: 0.3s">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Parents Directory</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span class="font-semibold"><?= $parents->num_rows ?></span> parents
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Parent Information</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact Details</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Occupation</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Students</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <?php 
                        $i = 1; 
                        $parents->data_seek(0); // Reset pointer for iteration
                        while($row = $parents->fetch_assoc()): 
                        ?>
                        <tr class="table-row-hover bg-white dark:bg-gray-900/50 hover:bg-gray-50 dark:hover:bg-gray-800/70 animate-slide-in" style="animation-delay: <?= $i*0.05 ?>s">
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= $i++ ?></div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-r from-primary-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                        <?= substr($row['name'], 0, 1) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($row['name']) ?></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            Registered: <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm text-gray-900 dark:text-white">
                                        <i class="fas fa-phone-alt mr-2 text-gray-400 w-4"></i>
                                        <span><?= $row['phone'] ?></span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-900 dark:text-white">
                                        <i class="fas fa-envelope mr-2 text-gray-400 w-4"></i>
                                        <span class="truncate max-w-[180px]" title="<?= $row['email'] ?>">
                                            <?= $row['email'] ? htmlspecialchars($row['email']) : '<span class="text-gray-400 italic">Not provided</span>' ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <?php if($row['occupation']): ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fas fa-briefcase mr-2"></i>
                                        <?= htmlspecialchars($row['occupation']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                        <i class="fas fa-question-circle mr-2"></i>
                                        Not specified
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex flex-col items-start">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold <?= $row['total_students'] > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300' ?>">
                                        <i class="fas fa-user-graduate mr-2"></i>
                                        <?= $row['total_students'] ?> student(s)
                                    </span>
                                    <?php if($row['total_students'] > 0): ?>
                                       <!-- <a href="parent_students.php?id=<?= $row['id'] ?>"
   class="inline-flex items-center text-xs text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 mt-1">
    <i class="fas fa-external-link-alt mr-1"></i>
    View students
</a> -->

                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- Details Button -->
                                    <button onclick='openDetails(<?= json_encode($row) ?>)' 
                                            class="inline-flex items-center gap-1 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white px-3 py-2 rounded-lg transition-colors group shadow-sm">
                                        <i class="fas fa-info-circle group-hover:scale-110 transition-transform"></i>
                                        <span class="hidden sm:inline">Details</span>
                                    </button>
                                    
                                    <!-- Full View Button -->
                                    <a href="parent_view.php?id=<?= $row['id'] ?>"
                                       class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white px-3 py-2 rounded-lg transition-colors group shadow-sm">
                                        <i class="fas fa-external-link-alt group-hover:rotate-12 transition-transform"></i>
                                        <span class="hidden sm:inline">Full View</span>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="edit_parent.php?id=<?= $row['id'] ?>"
                                       class="inline-flex items-center gap-1 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-3 py-2 rounded-lg transition-colors group shadow-sm">
                                        <i class="fas fa-edit group-hover:rotate-12 transition-transform"></i>
                                        <span class="hidden sm:inline">Edit</span>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <a onclick="return confirmDelete(<?= $row['id'] ?>, '<?= addslashes($row['name']) ?>', <?= $row['total_students'] ?>)" 
                                       href="delete_parent.php?id=<?= $row['id'] ?>"
                                       class="inline-flex items-center gap-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-3 py-2 rounded-lg transition-colors group shadow-sm">
                                        <i class="fas fa-trash-alt group-hover:scale-110 transition-transform"></i>
                                        <span class="hidden sm:inline">Delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if($parents->num_rows == 0): ?>
                        <tr>
                            <td colspan="6" class="py-16 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-gray-100 dark:bg-gray-800 p-8 mb-6">
                                        <i class="fas fa-user-slash text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-3">No parents found</h3>
                                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                                        <?php if($search): ?>
                                            No parents match your search for "<?= htmlspecialchars($search) ?>". Try a different search term.
                                        <?php else: ?>
                                            No parents are registered in the system yet. Add your first parent to get started.
                                        <?php endif; ?>
                                    </p>
                                    <a href="add_parents.php" class="bg-gradient-to-r from-primary-500 to-primary-700 hover:from-primary-600 hover:to-primary-800 text-white font-medium px-6 py-3 rounded-xl shadow-lg transition-colors">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Add Your First Parent
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Table Footer -->
            <?php if($parents->num_rows > 0): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-0">
                    Showing <span class="font-semibold"><?= $parents->num_rows ?></span> of <span class="font-semibold"><?= $totalParents ?></span> parents
                </div>
                <div class="flex items-center space-x-2">
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors disabled:opacity-50" disabled>
                        <i class="fas fa-chevron-left mr-2"></i>
                        Previous
                    </button>
                    <span class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Page 1</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors disabled:opacity-50" disabled>
                        Next
                        <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- =========================
         DETAILS POPUP MODAL (Enhanced)
    ========================= -->
    <div id="detailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/70 dark:bg-gray-900/90 backdrop-blur-sm transition-opacity" onclick="closeDetails()"></div>
            
            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden transform transition-all modal-enter w-full max-w-md">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                
                <div class="px-6 pt-6 pb-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700">
                                <i class="fas fa-user-circle text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Parent Details</h3>
                        </div>
                        <button onclick="closeDetails()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                <span id="d_initials">P</span>
                            </div>
                            <div>
                                <h4 id="d_name" class="font-bold text-gray-900 dark:text-white text-lg"></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Parent</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-phone-alt text-gray-400 text-sm"></i>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</span>
                                </div>
                                <p id="d_phone" class="text-gray-900 dark:text-white font-medium"></p>
                            </div>
                            
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</span>
                                </div>
                                <p id="d_email" class="text-gray-900 dark:text-white font-medium"></p>
                            </div>
                            
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-briefcase text-gray-400 text-sm"></i>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Occupation</span>
                                </div>
                                <p id="d_occupation" class="text-gray-900 dark:text-white font-medium"></p>
                            </div>
                            
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</span>
                                </div>
                                <p id="d_address" class="text-gray-900 dark:text-white font-medium"></p>
                            </div>
                            
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-calendar-plus text-gray-400 text-sm"></i>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registered</span>
                                </div>
                                <p id="d_created" class="text-gray-900 dark:text-white font-medium"></p>
                            </div>
                            
                            <div class="p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-100 dark:border-green-800/30">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-user-graduate text-green-500 text-sm"></i>
                                    <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Linked Students</span>
                                </div>
                                <p id="d_students" class="text-green-700 dark:text-green-300 font-bold text-lg"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                        <button onclick="closeDetails()" 
                                class="px-5 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================
         LINKED STUDENTS MODAL
    ========================= -->
    <div id="studentsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/70 dark:bg-gray-900/90 backdrop-blur-sm transition-opacity" onclick="closeStudentsModal()"></div>
            
            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden transform transition-all modal-enter w-full max-w-2xl">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-600"></div>
                
                <div class="px-6 pt-6 pb-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-gradient-to-br from-green-500 to-emerald-700">
                                <i class="fas fa-user-graduate text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Linked Students</h3>
                        </div>
                        <button onclick="closeStudentsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <p id="studentsModalTitle" class="text-gray-600 dark:text-gray-400 mt-2 ml-11"></p>
                </div>
                
                <div class="px-6 py-4">
                    <div id="studentsList" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        <!-- Students will be loaded here via AJAX -->
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                        <button onclick="closeStudentsModal()" 
                                class="px-5 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================
         JS
    ========================= -->
    <script>
        function openDetails(data){
            document.getElementById('detailsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Set initials
            document.getElementById('d_initials').textContent = data.name.charAt(0).toUpperCase();
            
            // Set data
            document.getElementById('d_name').textContent = data.name;
            document.getElementById('d_phone').textContent = data.phone;
            document.getElementById('d_email').textContent = data.email || 'Not provided';
            document.getElementById('d_occupation').textContent = data.occupation || 'Not specified';
            document.getElementById('d_address').textContent = data.address || 'Not provided';
            document.getElementById('d_created').textContent = formatDate(data.created_at);
            document.getElementById('d_students').textContent = data.total_students + ' student(s)';
        }

        function closeDetails(){
            document.getElementById('detailsModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function openStudentsModal(parentId, parentName){
            document.getElementById('studentsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            document.getElementById('studentsModalTitle').textContent = `Students linked to ${parentName}`;
            
            // Load students via AJAX
            fetch(`get_linked_students.php?parent_id=${parentId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('studentsList');
                    if(data.students && data.students.length > 0){
                        container.innerHTML = data.students.map(student => `
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                                            ${student.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">${student.name}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Class: ${student.class_name || 'Not assigned'}</p>
                                        </div>
                                    </div>
                                    <a href="../students/details.php?id=${student.id}" class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View
                                    </a>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = `
                            <div class="text-center py-8">
                                <div class="rounded-full bg-gray-100 dark:bg-gray-800 p-6 inline-block mb-4">
                                    <i class="fas fa-user-slash text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No students are linked to this parent.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    document.getElementById('studentsList').innerHTML = `
                        <div class="text-center py-8">
                            <div class="rounded-full bg-red-100 dark:bg-red-900/30 p-6 inline-block mb-4">
                                <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                            </div>
                            <p class="text-red-500 dark:text-red-400">Error loading students. Please try again.</p>
                        </div>
                    `;
                });
        }
        
        function closeStudentsModal(){
            document.getElementById('studentsModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function viewLinkedStudents(parentId){
            // Get parent name from table row
            const parentName = document.querySelector(`a[href="delete_parent.php?id=${parentId}"]`).closest('tr').querySelector('td:nth-child(2) div.ml-4 div').textContent;
            openStudentsModal(parentId, parentName);
        }
        
        function confirmDelete(id, name, studentCount){
            let message = `Are you sure you want to delete parent "${name}"?`;
            
            if(studentCount > 0){
                message += `\n\n⚠️ WARNING: This parent is linked to ${studentCount} student(s). Deleting them may affect student records.`;
            }
            
            return confirm(message);
        }
        
        function exportParents(){
            // In a real implementation, this would generate a CSV/Excel file
            alert('Export functionality would generate a CSV file with all parent data. This feature would be implemented in a production environment.');
        }
        
        function formatDate(dateString){
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC to close modals
            if (e.key === 'Escape') {
                closeDetails();
                closeStudentsModal();
            }
            
            // Ctrl/Cmd + F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                document.querySelector('input[name="search"]').focus();
            }
        });
        
        // Add ripple effect to buttons
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button, a.btn-effect');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.7);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        top: ${y}px;
                        left: ${x}px;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
            
            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                
                .btn-effect {
                    position: relative;
                    overflow: hidden;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>