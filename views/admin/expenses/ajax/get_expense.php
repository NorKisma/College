<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';
 
if (!isset($_SESSION['user_id'])) {
    echo 'Not authenticated';
    exit;
}

$id = intval($_GET['id']);
$action = $_GET['action'] ?? 'view';

$stmt = $conn->prepare("
    SELECT e.*, u.first_name, u.last_name, u.username
    FROM expenses e
    LEFT JOIN users u ON e.recorded_by = u.id
    WHERE e.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="alert alert-danger">Expense not found</div>';
    exit;
}

$expense = $result->fetch_assoc();

if ($action === 'view') {
    // View mode
    ?>
    <div class="expense-details">
        <div class="text-center mb-4">
            <div class="amount-display display-4 fw-bold text-danger">
                $<?= number_format($expense['amount'], 2) ?>
            </div>
            <div class="text-muted">Expense Amount</div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <div class="detail-item mb-3">
                    <div class="detail-label">Title</div>
                    <div class="detail-value fw-bold"><?= htmlspecialchars($expense['title']) ?></div>
                </div>
                
                <div class="detail-item mb-3">
                    <div class="detail-label">Category</div>
                    <div class="detail-value">
                        <span class="badge bg-secondary">
                            <?= htmlspecialchars($expense['category'] ?: 'Other') ?>
                        </span>
                    </div>
                </div>
                
                <div class="detail-item mb-3">
                    <div class="detail-label">Expense Date</div>
                    <div class="detail-value">
                        <i class="bi bi-calendar-event me-2"></i>
                        <?= date('F d, Y', strtotime($expense['expense_date'])) ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="detail-item mb-3">
                    <div class="detail-label">Recorded By</div>
                    <div class="detail-value">
                        <i class="bi bi-person-circle me-2"></i>
                        <?= htmlspecialchars($expense['first_name'] . ' ' . $expense['last_name']) ?>
                        <small class="text-muted">(@<?= htmlspecialchars($expense['username']) ?>)</small>
                    </div>
                </div>
                
                <div class="detail-item mb-3">
                    <div class="detail-label">Created On</div>
                    <div class="detail-value">
                        <i class="bi bi-clock-history me-2"></i>
                        <?= date('F d, Y H:i:s', strtotime($expense['created_at'])) ?>
                    </div>
                </div>
                
                <div class="detail-item mb-3">
                    <div class="detail-label">Expense ID</div>
                    <div class="detail-value">
                        <code>EXP-<?= str_pad($expense['id'], 6, '0', STR_PAD_LEFT) ?></code>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($expense['notes'] ?? ''): ?>
            <hr>
            <div class="detail-item">
                <div class="detail-label">Additional Notes</div>
                <div class="detail-value">
                    <div class="card bg-light">
                        <div class="card-body">
                            <?= nl2br(htmlspecialchars($expense['notes'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
} elseif ($action === 'edit') {
    // Edit mode - form
    ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" required 
                   value="<?= htmlspecialchars($expense['title']) ?>">
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label">Category</label>
            <?php
            // Fetch categories for the dropdown
            $categories_res = $conn->query("SELECT DISTINCT category FROM expenses WHERE category IS NOT NULL AND category != '' ORDER BY category");
            $categories = [];
            while($cat = $categories_res->fetch_assoc()) {
                $categories[] = $cat['category'];
            }
            ?>
            <select name="category" class="form-select tom-select-create" placeholder="Select Category">
                <option value="">Select Category</option>
                <option value="Salaries" <?= $expense['category'] == 'Salaries' ? 'selected' : '' ?>>Salaries</option>
                <option value="Utilities" <?= $expense['category'] == 'Utilities' ? 'selected' : '' ?>>Utilities</option>
                <option value="Maintenance" <?= $expense['category'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                <option value="Supplies" <?= $expense['category'] == 'Supplies' ? 'selected' : '' ?>>Supplies</option>
                <option value="Equipment" <?= $expense['category'] == 'Equipment' ? 'selected' : '' ?>>Equipment</option>
                <option value="Transportation" <?= $expense['category'] == 'Transportation' ? 'selected' : '' ?>>Transportation</option>
                <option value="Training" <?= $expense['category'] == 'Training' ? 'selected' : '' ?>>Training</option>
                <option value="Marketing" <?= $expense['category'] == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                <option value="Other" <?= $expense['category'] == 'Other' ? 'selected' : '' ?>>Other</option>
                <?php foreach ($categories as $cat): 
                    // Skip default categories if they are already added above
                    $defaults = ['Salaries', 'Utilities', 'Maintenance', 'Supplies', 'Equipment', 'Transportation', 'Training', 'Marketing', 'Other'];
                    if (in_array($cat, $defaults)) continue;
                ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $expense['category'] == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
                <?php 
                // If current category is custom and not in list, add it
                if ($expense['category'] && !in_array($expense['category'], array_merge($defaults, $categories))): ?>
                    <option value="<?= htmlspecialchars($expense['category']) ?>" selected>
                        <?= htmlspecialchars($expense['category']) ?>
                    </option>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label">Amount *</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="amount" class="form-control" 
                       step="0.01" min="0.01" required 
                       value="<?= number_format($expense['amount'], 2) ?>">
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label">Date *</label>
            <input type="date" name="expense_date" class="form-control" 
                   value="<?= $expense['expense_date'] ?>" required>
        </div>
        
        <div class="col-12 mb-3">
            <label class="form-label">Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($expense['notes'] ?? '') ?></textarea>
        </div>
        
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                This expense was recorded by: 
                <strong><?= htmlspecialchars($expense['first_name'] . ' ' . $expense['last_name']) ?></strong>
                on <?= date('M d, Y', strtotime($expense['created_at'])) ?>
            </div>
        </div>
    </div>
    <?php
}
?>