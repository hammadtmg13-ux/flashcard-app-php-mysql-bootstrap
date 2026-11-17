<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $color = trim($_POST['color'] ?? 'primary');

    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO decks (name, description, color) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $color]);
    }
}

header('Location: index.php');
exit;
