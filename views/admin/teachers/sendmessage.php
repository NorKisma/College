<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ========= SECURITY CHECK (IMPORTANT) ========= */
if(!isset($_SESSION['user_id'])){
    die("Access denied");
}

/* ========= GET RECEIVER EMAIL ========= */
$toEmail = $_GET['to'] ?? '';
$teacher_id = $_GET['teacher_id'] ?? '';

/* ========= OPTIONAL: VERIFY EMAIL EXISTS ========= */
$checkStmt = $conn->prepare("SELECT email FROM teachers WHERE id=? LIMIT 1");
$checkStmt->bind_param("i", $teacher_id);
$checkStmt->execute();
$checkRes = $checkStmt->get_result();

if($checkRes->num_rows == 0){
    die("Invalid recipient");
}

$teacher = $checkRes->fetch_assoc();

if($teacher['email'] !== $toEmail){
    die("Email mismatch detected");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Message</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-6">

<div class="bg-gray-800 p-8 rounded-xl w-full max-w-lg">
    <h2 class="text-2xl text-white font-bold mb-6">Send Message</h2>

    <form action="sendmessagehandler.php" method="POST" class="space-y-4">

        <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">

        <!-- Receiver Email (AUTO FILLED & LOCKED) -->
        <div>
            <label class="text-gray-300 text-sm">Receiver Email</label>
            <input type="email"
                   name="to_email"
                   value="<?= htmlspecialchars($toEmail) ?>"
                   readonly
                   class="w-full mt-1 p-3 rounded bg-gray-700 text-white border border-gray-600">
        </div>

        <!-- Subject -->
        <div>
            <label class="text-gray-300 text-sm">Subject</label>
            <input type="text" name="subject" required
                   class="w-full mt-1 p-3 rounded bg-gray-700 text-white border border-gray-600">
        </div>

        <!-- Message -->
        <div>
            <label class="text-gray-300 text-sm">Message</label>
            <textarea name="message" rows="5" required
                      class="w-full mt-1 p-3 rounded bg-gray-700 text-white border border-gray-600"></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-between mt-6">
            <a href="teacher_view.php?id=<?= $teacher_id ?>"
               class="px-5 py-2 bg-gray-600 text-white rounded">Cancel</a>

            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Send Message
            </button>
        </div>

    </form>
</div>

</body>
</html>
