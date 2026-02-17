<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

 

$teacher_id = $_SESSION['user_id'];
$error = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language_id = (int)($_POST['language_id'] ?? 0);

    if ($language_id > 0) {
        $stmt = $conn->prepare("UPDATE teachers SET language_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $language_id, $teacher_id);
        $stmt->execute();

        // optional: update session
        $_SESSION['language_id'] = $language_id;

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Please select a language.";
    }
}

// Fetch languages
$langs = $conn->query("SELECT id, name FROM languages ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Choose Language</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm p-4" style="max-width: 420px; width:100%;">
        <h4 class="mb-3 text-center">Choose Your Language</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Language</label>
                <select name="language_id" class="form-select" required>
                    <option value="">-- Select Language --</option>
                    <?php while ($l = $langs->fetch_assoc()): ?>
                        <option value="<?= $l['id'] ?>">
                            <?= htmlspecialchars($l['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Save & Continue
            </button>
        </form>
    </div>
</div>

</body>
</html>
