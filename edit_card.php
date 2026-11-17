<?php
require 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM cards WHERE id = ?");
$stmt->execute([$id]);
$card = $stmt->fetch();

if (!$card) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    if ($question !== '' && $answer !== '') {
        $stmt = $pdo->prepare("UPDATE cards SET question = ?, answer = ? WHERE id = ?");
        $stmt->execute([$question, $answer, $id]);
    }
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flashcard - FlashLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-5" style="max-width: 640px;">
    <a href="index.php" class="btn btn-outline-secondary mb-4">&larr; Back</a>
    <div class="card p-4">
        <h3 class="mb-3">Edit Flashcard</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Question</label>
                <textarea name="question" class="form-control" rows="2" required><?php echo htmlspecialchars($card['question']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Answer</label>
                <textarea name="answer" class="form-control" rows="3" required><?php echo htmlspecialchars($card['answer']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
