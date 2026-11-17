<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deckId = (int)($_POST['deck_id'] ?? 0);
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');

    if ($deckId > 0 && $question !== '' && $answer !== '') {
        $stmt = $pdo->prepare("INSERT INTO cards (deck_id, question, answer) VALUES (?, ?, ?)");
        $stmt->execute([$deckId, $question, $answer]);
    }
}

header('Location: index.php');
exit;
