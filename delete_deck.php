<?php
require 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM decks WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
