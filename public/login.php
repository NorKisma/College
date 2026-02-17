<?php
// login.php
session_start();
require_once __DIR__ . '/../config/db.php';
$slides = $conn->query("
    SELECT image_path 
    FROM login_slideshow 
    WHERE is_active = 1
    ORDER BY position ASC
    LIMIT 6
")->fetch_all(MYSQLI_ASSOC);

// Count total students
$studentCount = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'] ?? 0;

// Count total teachers (optional: only active teachers)
$teacherCount = $conn->query("SELECT COUNT(*) AS total FROM teachers WHERE status='active'")->fetch_assoc()['total'] ?? 0;
$query = "SELECT COUNT(*) as total_alumni FROM alumni";
$result = $conn->query($query);
$alumni_count = 0;

if ($result) {
    $row = $result->fetch_assoc();
    $alumni_count = $row['total_alumni'];
}

// Fetch latest app settings
$settings = $conn->query("SELECT * FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();

// Default fallback
$app_name = $settings['app_name'] ?? 'Guriga Cilmiga';
$logo_path = $settings['logo_path'] ?? 'logo.png';
$show_type = $settings['show_type'] ?? 'text';


$error = '';

// If user is already logged in, redirect to their dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: ../views/admin/dashboard.php');
            break;
        case 'teacher':
            header('Location: ../views/teachers/dashboard.php');
            break;
        case 'parent':
            header('Location: ../views/parent/parent_portal.php');
            break;
        default:
            $error = "Unknown role, cannot redirect.";
            break;
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifier = trim($_POST['email'] ?? '');   // email OR phone
    $secret     = trim($_POST['password'] ?? ''); // password OR pin

    if ($identifier === '' || $secret === '') {
        $error = 'Please enter required credentials.';
    } else {

        /* ======================
           1️⃣ ADMIN LOGIN
           ====================== */
        $stmt = $conn->prepare("
            SELECT u.id, u.first_name, u.last_name, u.password, r.name AS role
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $adminRes = $stmt->get_result();

        if ($adminRes->num_rows === 1) {
            $admin = $adminRes->fetch_assoc();

            // Check using password_verify for hashed passwords
            // If it fails, check plain text (for backward compatibility during migration)
            if (password_verify($secret, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['role']    = $admin['role'];
                $_SESSION['name']    = trim($admin['first_name'].' '.$admin['last_name']);

                header("Location: /College/views/admin/dashboard.php");
                exit;
            } elseif ($secret === $admin['password']) {
                // FALLBACK: If password is not yet hashed, allow plain text
                // then hash it immediately for future logins
                $hashed = password_hash($secret, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed, $admin['id']);
                $update_stmt->execute();

                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['role']    = $admin['role'];
                $_SESSION['name']    = trim($admin['first_name'].' '.$admin['last_name']);

                header("Location: /College/views/admin/dashboard.php");
                exit;
            }
        }

        /* ======================
           2️⃣ TEACHER LOGIN
           email OR phone + PIN
           ====================== */
        $stmt = $conn->prepare("
            SELECT id, first_name, last_name, login_pin, status
            FROM teachers
            WHERE (email = ? OR phone = ?)
            LIMIT 1
        ");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $teacherRes = $stmt->get_result();

        if ($teacherRes->num_rows === 1) {
            $teacher = $teacherRes->fetch_assoc();

            if ($teacher['status'] !== 'active') {
                $error = 'Teacher account is not active.';
            } else {
                // Verify PIN (hashed or plain fallback)
                if (password_verify($secret, $teacher['login_pin'])) {
                    $_SESSION['user_id'] = $teacher['id'];
                    $_SESSION['role']    = 'teacher';
                    $_SESSION['name']    = trim($teacher['first_name'].' '.$teacher['last_name']);

                    header("Location: /College/views/teachers/dashboard.php");
                    exit;
                } elseif ($secret === $teacher['login_pin']) {
                    // FALLBACK: Auto-hash on first login
                    $hashed = password_hash($secret, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE teachers SET login_pin = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $hashed, $teacher['id']);
                    $update_stmt->execute();

                    $_SESSION['user_id'] = $teacher['id'];
                    $_SESSION['role']    = 'teacher';
                    $_SESSION['name']    = trim($teacher['first_name'].' '.$teacher['last_name']);

                    header("Location: /College/views/teachers/dashboard.php");
                    exit;
                }
            }
        }

        /* ======================
           3️⃣ PARENT LOGIN
           phone OR email + PIN
           ====================== */
        $stmt = $conn->prepare("
            SELECT id, name, login_pin
            FROM parents
            WHERE (phone = ? OR email = ?)
            LIMIT 1
        ");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $parentRes = $stmt->get_result();

        if ($parentRes->num_rows === 1) {
            $parent = $parentRes->fetch_assoc();

            if (password_verify($secret, $parent['login_pin'])) {
                $_SESSION['user_id']     = $parent['id'];
                $_SESSION['role']        = 'parent';
                $_SESSION['parent_name'] = $parent['name'];
                $_SESSION['parent_phone']= $identifier;

                header("Location: /College/views/parent/parent_portal.php");
                exit;
            } elseif ($secret === $parent['login_pin']) {
                // FALLBACK: Auto-hash on first login
                $hashed = password_hash($secret, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE parents SET login_pin = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed, $parent['id']);
                $update_stmt->execute();

                $_SESSION['user_id']     = $parent['id'];
                $_SESSION['role']        = 'parent';
                $_SESSION['parent_name'] = $parent['name'];
                $_SESSION['parent_phone']= $identifier;

                header("Location: /College/views/parent/parent_portal.php");
                exit;
            }
        }

        /* ======================
           ❌ FAILED LOGIN
           ====================== */
        $error = 'Invalid login credentials.';
    }
}

?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guriga Cilmiga - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d3748;
            --accent-color: #e53e3e;
            --gold-color: #d4af37;
            --light-color: #f7fafc;
            --dark-color: #1a202c;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--dark-color);
            color: var(--light-color);
            overflow-x: hidden;
        }

        .container {
            display: flex;
            flex-direction: column; /* default mobile */
            min-height: 100vh;
        }
 
       /* Background Slideshow */
        .background-slideshow {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
        }

        .background-slideshow .slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            animation-name: fadeSlide;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
        } 

    /* Smooth fade animation */
        @keyframes fadeSlide {
            0%   { opacity: 0; }
            8%   { opacity: 1; }
            30%  { opacity: 1; }
            38%  { opacity: 0; }
            100% { opacity: 0; }
        }


    /* Optional: remove dark overlay (you asked for transparent) */
    .slide::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: transparent;
    }

            /* Left Section - Login Form */
            .login-section {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: rgba(26, 32, 44, 0.8);
            /* backdrop-filter: blur(10px); */
            position: relative;
            z-index: 1;
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 1rem;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
            padding: 5px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .school-name {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(to right, var(--gold-color), #f7ef8a);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);
        }

        .login-box {
            background: rgba(255, 255, 255, 0.1);
            /* backdrop-filter: blur(15px); */
            border-radius: 15px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
        }

        .login-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: var(--gold-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light-color);
            font-size: 1rem;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-group input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--gold-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 2.7rem;
            color: var(--gold-color);
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--gold-color), #b8941f);
            border: none;
            border-radius: 8px;
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #b8941f, var(--gold-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-password a {
            color: var(--gold-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .error {
            background: rgba(229, 62, 62, 0.2);
            padding: 1rem;
            color: #fc8181;
            margin-bottom: 1rem;
            border-radius: 8px;
            border-left: 4px solid var(--accent-color);
        }

        /* Right Section - School Info */
        .info-section {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            background: rgba(26, 32, 44, 0.7);
            /* backdrop-filter: blur(10px); */
            position: relative;
            z-index: 1;
        }

        .info-section h2 {
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: var(--gold-color);
            text-align: center;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .feature {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .feature i {
            font-size: 2rem;
            color: var(--gold-color);
            margin-bottom: 1rem;
        }

        .feature h3 {
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 2rem;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gold-color);
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Language Selector */
        .language-selector {
            position: absolute;
            top: 2rem;
            right: 2rem;
            display: flex;
            gap: 0.5rem;
            z-index: 10;
        }

        .lang-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .lang-btn.active {
            background: var(--gold-color);
            color: var(--primary-color);
            font-weight: 600;
        }

        .lang-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Footer */
        footer {
            position: relative;
            bottom: 0;
            margin-top:auto;
            left: 0;
            width: 100%;
            padding: 1.5rem;
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            z-index: 1;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            color: var(--light-color);
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            color: var(--gold-color);
            transform: translateY(-3px);
        }

        .copyright {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .info-section {
                order: -1;
                flex: 1;
                justify-content: center;
                padding-top: 4rem;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
            
            .language-selector {
                top: 1rem;
                right: 1rem;
            }
        }

        @media (max-width: 576px) {
            .login-box, .info-section {
                padding: 1.5rem;
            }
            
            .school-name {
                font-size: 1.5rem;
            }
            
            .stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background Slideshow -->
  <div class="background-slideshow">
    <?php
        $slideCount = count($slides);
        $durationPerSlide = 6; // seconds
        $totalDuration = $slideCount * $durationPerSlide;
    ?>
    
    <?php foreach ($slides as $index => $slide): ?>
        <div class="slide"
             style="
                background-image: url('/College/<?php echo htmlspecialchars($slide['image_path']); ?>');
                animation-delay: <?php echo $index * $durationPerSlide; ?>s;
                animation-duration: <?php echo $totalDuration; ?>s;
             ">
        </div>
    <?php endforeach; ?>
</div>
</div></div>

    <!-- Language Selector -->
    <div class="language-selector">
        <button class="lang-btn active" data-lang="so">Soomaali</button>
        <button class="lang-btn" data-lang="en">English</button>
        <button class="lang-btn" data-lang="ar">العربية</button>
    </div>

    <div class="container">
        <!-- Login Section -->
        <section class="login-section">
    <div class="logo-container">
    <?php if (!empty($logo_path)): ?>
        <div class="logo">
            <img src="/College/<?= htmlspecialchars($logo_path) ?>" alt="<?= htmlspecialchars($app_name) ?>">
        </div>
    <?php endif; ?>
    <h1 class="school-name" data-so="<?= htmlspecialchars($app_name) ?>">
        <?= htmlspecialchars($app_name) ?>
    </h1>
</div>


            <div class="login-box">
                <h2 data-so="Geli Akoonka" data-en="Login" data-ar="تسجيل الدخول">Geli Akoonka</h2>
                
                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email" data-so="Cinwaanka Email" data-en="Email Address" data-ar="البريد الإلكتروني">Cinwaanka Email</label>
                        <i class="fas fa-envelope"></i>
                    <input type="text"
                        id="email"
                        name="email"
                        placeholder="Email or Phone"
                        required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" data-so="Furaha Sirta" data-en="Password" data-ar="كلمة المرور">Furaha Sirta</label>
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Gali furaha sirta" data-so-placeholder="Gali furaha sirta" data-en-placeholder="Enter your password" data-ar-placeholder="أدخل كلمة المرور" required>
                    </div>
                    
                    <button type="submit" class="login-btn" data-so="Geli" data-en="Login" data-ar="دخول">Geli</button>
                </form>
                
                <div class="forgot-password">
                    <a href="#" data-so="Ma ilowday furaha sirta?" data-en="Forgot your password?" data-ar="نسيت كلمة المرور؟">Ma ilowday furaha sirta?</a>
                </div>
            </div>
        </section>

        <!-- Info Section -->
        <section class="info-section">
            <h2 data-so="Waxyaabaha Naga Muuga" data-en="What Makes Us Special" data-ar="ما يميزنا">Waxyaabaha gooniga noo ah
            </h2>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-user-graduate"></i>
                    <h3 data-so="Tacliin Tayo leh" data-en="Quality Education" data-ar="تعليم ذو جودة">Tacliin Tayo leh</h3>
                    <p data-so="Waxaan bixinaa tacliinta ugu fiican qalab casri ah" data-en="We provide the best education with modern facilities" data-ar="نقدم أفضل تعليم بمرافق حديثة">Waxaan bixinaa tacliinta ugu fiican qalab casri ah</p>
                </div>
                
                <div class="feature">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3 data-so="Macalimiin Khibrad leh" data-en="Experienced Teachers" data-ar="معلمون ذوو خبرة">Macalimiin Khibrad leh</h3>
                    <p data-so="Macalimiin khibrad badan oo u diyaarsan inay ku caawiyaan" data-en="Highly experienced teachers ready to assist you" data-ar="معلمون ذوو خبرة عالية مستعدون لمساعدتك">Macalimiin khibrad badan oo u diyaarsan inay ku caawiyaan</p>
                </div>
                
                <div class="feature">
                    <i class="fas fa-flask"></i>
                    <h3 data-so="Maktabadaha Casriga ah" data-en="Modern Laboratories" data-ar="مختبرات حديثة">Maktabadaha Casriga ah</h3>
                    <p data-so="Maktabado casri ah oo loogu talagalay waxbarasha saameyn leh" data-en="Modern laboratories for safe and effective learning" data-ar="مختبرات حديثة للتعلم الآمن والفعال">Maktabado casri ah oo loogu talagalay waxbarasha waxtar leh</p>
                </div>
                
                <div class="feature">
                    <i class="fas fa-medal"></i>
                    <h3 data-so="Guulaha sare" data-en="High Achievements" data-ar="إنجازات عالية">Guulaha sare</h3>
                    <p data-so="Ardayda waxay gaaraan natiijooyin sare oo guulo badan" data-en="Our students achieve high results and many successes" data-ar="يحقق طلابنا نتائج عالية ونجاحات كثيرة">Ardayda waxay gaaraan natiijooyin sare oo guulo badan</p>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat">
                    <div class="stat-number"><?= $alumni_count ?></div>
                    <div class="stat-label" data-so="Guulaha Ardayda" data-en="Student Success" data-ar="نجاح الطلاب">Guulaha Ardayda</div>
                </div>
                
                <div class="stat">
                    
                    <div class="stat-number"><?= htmlspecialchars($teacherCount) ?>+</div>

                    <div class="stat-label" data-so="Macalimiin" data-en="Teachers" data-ar="المعلمون">Macalimiin</div>
                </div>
                
                <div class="stat">
                    
                    <div class="stat-number"><?= htmlspecialchars($studentCount) ?>+</div>
                    <div class="stat-label" data-so="Arday" data-en="Students" data-ar="الطلاب">Arday</div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
  <footer>
    <div class="footer-content">
        <div class="copyright" 
             data-so="© <?= date('Y') ?> Guriga Cilmiga. Dhammaan Xuquuqaha Way Dhawrsan Yihiin."
             data-en="© <?= date('Y') ?> House of Knowledge. All Rights Reserved."
             data-ar="© <?= date('Y') ?> بيت المعرفة. جميع الحقوق محفوظة.">
            © <?= date('Y') ?> Guriga Cilmiga. Dhammaan Xuquuqaha Way Dhawrsan Yihiin.
        </div>
        
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
</footer>


    <script>
        // Language Switching
        document.addEventListener('DOMContentLoaded', function() {
            const langButtons = document.querySelectorAll('.lang-btn');
            const elementsToTranslate = document.querySelectorAll('[data-so], [data-en], [data-ar]');
            
            langButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const selectedLang = this.getAttribute('data-lang');
                    
                    // Update active button
                    langButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update page language
                    document.documentElement.lang = selectedLang;
                    
                    // Translate all elements
                    elementsToTranslate.forEach(element => {
                        const translation = element.getAttribute(`data-${selectedLang}`);
                        if (translation) {
                            if (element.hasAttribute('data-so-placeholder')) {
                                element.placeholder = element.getAttribute(`data-${selectedLang}-placeholder`);
                            } else {
                                element.textContent = translation;
                            }
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
