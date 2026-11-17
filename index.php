<?php
require 'config.php';

// Stats
$totalDecks = $pdo->query("SELECT COUNT(*) FROM decks")->fetchColumn();
$totalCards = $pdo->query("SELECT COUNT(*) FROM cards")->fetchColumn();
$masteredCards = $pdo->query("SELECT COUNT(*) FROM cards WHERE mastered = 1")->fetchColumn();
$masteryPct = $totalCards > 0 ? round($masteredCards / $totalCards * 100) : 0;

// Decks with card counts
$decks = $pdo->query("
    SELECT d.*, COUNT(c.id) AS card_count,
           SUM(CASE WHEN c.mastered = 1 THEN 1 ELSE 0 END) AS mastered_count
    FROM decks d
    LEFT JOIN cards c ON c.deck_id = d.id
    GROUP BY d.id
    ORDER BY d.created_at DESC
")->fetchAll();

// All cards with deck info
$cards = $pdo->query("
    SELECT c.*, d.name AS deck_name, d.color AS deck_color
    FROM cards c
    JOIN decks d ON d.id = c.deck_id
    ORDER BY c.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlashLearn - Simple Flashcard Study App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="hero text-center">
    <div class="container">
        <h1>&#128218; FlashLearn</h1>
        <p class="lead mb-0">Create decks, flip flashcards and master any subject, one card at a time.</p>
    </div>
</header>

<div class="container pb-5">

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number text-primary"><?php echo $totalDecks; ?></div>
                <div class="text-muted">Decks</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number text-info"><?php echo $totalCards; ?></div>
                <div class="text-muted">Flashcards</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number text-success"><?php echo $masteredCards; ?></div>
                <div class="text-muted">Mastered</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number text-warning"><?php echo $masteryPct; ?>%</div>
                <div class="text-muted">Mastery</div>
            </div>
        </div>
    </div>

    <!-- Search and filter -->
    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search decks and cards...">
        </div>
        <div class="col-md-3">
            <select id="deckFilter" class="form-select form-select-lg">
                <option value="">All decks</option>
                <?php foreach ($decks as $deck): ?>
                    <option value="<?php echo $deck['id']; ?>"><?php echo htmlspecialchars($deck['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-grid">
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addDeckModal">+ New Deck</button>
        </div>
    </div>

    <!-- Decks -->
    <h3 class="mb-3">Your Decks</h3>
    <?php if (empty($decks)): ?>
        <div class="alert alert-info">No decks yet. Create your first deck to start studying!</div>
    <?php endif; ?>
    <div class="row g-3 mb-5">
        <?php foreach ($decks as $deck):
            $pct = $deck['card_count'] > 0 ? round($deck['mastered_count'] / $deck['card_count'] * 100) : 0;
        ?>
        <div class="col-md-4 deck-col">
            <div class="card deck-card h-100">
                <div class="deck-color-bar bg-<?php echo htmlspecialchars($deck['color']); ?>"></div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($deck['name']); ?></h5>
                    <p class="card-text text-muted"><?php echo htmlspecialchars($deck['description']); ?></p>
                    <p class="mb-1"><strong><?php echo $deck['card_count']; ?></strong> cards &middot; <strong><?php echo (int)$deck['mastered_count']; ?></strong> mastered</p>
                    <div class="progress progress-thin mb-3">
                        <div class="progress-bar bg-success" style="width: <?php echo $pct; ?>%"></div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php if ($deck['card_count'] > 0): ?>
                            <a href="study.php?deck_id=<?php echo $deck['id']; ?>" class="btn btn-primary btn-sm">&#9654; Study</a>
                        <?php endif; ?>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCardModal" data-deck-id="<?php echo $deck['id']; ?>" data-deck-name="<?php echo htmlspecialchars($deck['name']); ?>">+ Add Card</button>
                        <a href="delete_deck.php?id=<?php echo $deck['id']; ?>" class="btn btn-outline-danger btn-sm delete-confirm">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- All cards -->
    <h3 class="mb-3">All Flashcards</h3>
    <?php if (empty($cards)): ?>
        <div class="alert alert-info">No flashcards yet. Add a card to one of your decks.</div>
    <?php endif; ?>
    <div class="row g-3">
        <?php foreach ($cards as $card): ?>
        <div class="col-md-6">
            <div class="card card-item p-3 <?php echo $card['mastered'] ? 'mastered' : ''; ?>" data-deck-id="<?php echo $card['deck_id']; ?>">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge bg-<?php echo htmlspecialchars($card['deck_color']); ?> badge-deck"><?php echo htmlspecialchars($card['deck_name']); ?></span>
                    <?php if ($card['mastered']): ?><span class="badge bg-success">Mastered &#10003;</span><?php endif; ?>
                </div>
                <h5 class="mt-2"><?php echo htmlspecialchars($card['question']); ?></h5>
                <p class="answer-text text-muted mb-2"><?php echo htmlspecialchars($card['answer']); ?></p>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-secondary reveal-btn">Show Answer</button>
                    <a href="toggle_mastered.php?id=<?php echo $card['id']; ?>" class="btn btn-sm <?php echo $card['mastered'] ? 'btn-success' : 'btn-outline-success'; ?>">
                        <?php echo $card['mastered'] ? 'Mastered ✓' : 'Mark Mastered'; ?>
                    </a>
                    <a href="edit_card.php?id=<?php echo $card['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="delete_card.php?id=<?php echo $card['id']; ?>" class="btn btn-sm btn-outline-danger delete-confirm">Delete</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Deck Modal -->
<div class="modal fade" id="addDeckModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="add_deck.php">
            <div class="modal-header">
                <h5 class="modal-title">Create a New Deck</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Deck name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. PHP Basics" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="What is this deck about?">
                </div>
                <div class="mb-3">
                    <label class="form-label">Colour</label>
                    <select name="color" class="form-select">
                        <option value="primary">Blue</option>
                        <option value="success">Green</option>
                        <option value="warning">Yellow</option>
                        <option value="danger">Red</option>
                        <option value="info">Teal</option>
                        <option value="dark">Dark</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Deck</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Card Modal -->
<div class="modal fade" id="addCardModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="add_card.php">
            <div class="modal-header">
                <h5 class="modal-title">Add a Flashcard to <span id="addCardDeckName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="deck_id" id="addCardDeckId">
                <div class="mb-3">
                    <label class="form-label">Question</label>
                    <textarea name="question" class="form-control" rows="2" placeholder="What do you want to remember?" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Answer</label>
                    <textarea name="answer" class="form-control" rows="3" placeholder="The answer..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Card</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
// Fill the Add Card modal with the chosen deck
document.getElementById('addCardModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('addCardDeckId').value = button.getAttribute('data-deck-id');
    document.getElementById('addCardDeckName').textContent = button.getAttribute('data-deck-name');
});
</script>
</body>
</html>
