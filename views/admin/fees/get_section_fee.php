<?php
require_once __DIR__ . '/../../../config/db.php';
$section_id = (int)$_GET['section_id'];
$fee_category_id = (int)$_GET['fee_category_id'];
$row = $conn->query("
    SELECT amount
    FROM section_fees
    WHERE section_id = $section_id AND fee_category_id = $fee_category_id
    LIMIT 1
")->fetch_assoc();

echo json_encode(['amount'=>$row ? (float)$row['amount'] : 0]);
