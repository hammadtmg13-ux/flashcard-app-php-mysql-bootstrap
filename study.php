<?php
require 'config.php';

$deckId = isset($_GET['deck_id']) ? (int)$_GET['deck_id'] : 0;
$deck = $pdo->prepare("SELECT * FROM decks WHERE id = ?");
$deck->execute([$deckId]);
$deck = $deck->fetch();

if (!$deck) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cards WHERE deck_id = ? ORDER BY created_at ASC");
$stmt->execute([$deckId]);
$cards = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study: <?php echo htmlspecialchars($deck['name']); ?> - FlashLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="hero text-center">
    <div class="container">
        <h1>&#128218; Study: <?php echo htmlspecialchars($deck['name']); ?></h1>
        <p class="lead mb-0"><?php echo htmlspecialchars($deck['description']); ?></p>
    </div>
</header>

<div class="container pb-5" style="max-width: 720px;">
    <a href="index.php" class="btn btn-outline-secondary mb-4">&larr; Back to Decks</a>

    <?php if (empty($cards)): ?>
        <div class="alert alert-info">This deck has no cards yet. Add a card from the dashboard to start studying.</div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span id="studyCounter" class="fw-bold">Card 1 of <?php echo count($cards); ?></span>
            <button id="shuffleBtn" class="btn btn-outline-primary btn-sm">&#128256; Shuffle</button>
        </div>
        <div class="progress progress-thin mb-4">
            <div id="studyProgress" class="progress-bar bg-success" style="width: <?php echo round(100 / count($cards)); ?>%"></div>
        </div>

        <div class="flashcard-scene mb-4">
            <div class="flashcard" id="flashcard">
                <div class="flashcard-face flashcard-front">
                    <small class="text-uppercase text-muted fw-bold">Question</small>
                    <h3 id="studyQuestion" class="mt-3"><?php echo htmlspecialchars($cards[0]['question']); ?></h3>
                    <p class="flip-hint mt-4">Click the card to reveal the answer</p>
                </div>
                <div class="flashcard-face flashcard-back">
                    <small class="text-uppercase fw-bold" style="opacity:0.8">Answer</small>
                    <h4 id="studyAnswer" class="mt-3"><?php echo htmlspecialchars($cards[0]['answer']); ?></h4>
                    <p class="flip-hint mt-4">Click the card to see the question again</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between gap-2">
            <button id="prevCardBtn" class="btn btn-outline-secondary flex-fill">&larr; Previous</button>
            <button id="markMasteredBtn" class="btn btn-outline-success flex-fill">Mark as Mastered</button>
            <button id="nextCardBtn" class="btn btn-primary flex-fill">Next &rarr;</button>
        </div>
    <?php endif; ?>
</div>

<script>
window.studyCards = <?php echo json_encode(array_map(function ($c) {
    return ['id' => (int)$c['id'], 'question' => $c['question'], 'answer' => $c['answer'], 'mastered' => (int)$c['mastered']];
}, $cards)); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
