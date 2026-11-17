<?php
require 'config.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid card id']);
    exit;
}

$stmt = $pdo->prepare("SELECT mastered FROM cards WHERE id = ?");
$stmt->execute([$id]);
$card = $stmt->fetch();

if (!$card) {
    echo json_encode(['success' => false, 'message' => 'Card not found']);
    exit;
}

$newValue = $card['mastered'] ? 0 : 1;
$stmt = $pdo->prepare("UPDATE cards SET mastered = ? WHERE id = ?");
$stmt->execute([$newValue, $id]);

echo json_encode(['success' => true, 'mastered' => $newValue]);
