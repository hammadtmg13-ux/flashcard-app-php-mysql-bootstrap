// FlashLearn - Front-end interactions

document.addEventListener('DOMContentLoaded', function () {

    // Search cards and decks on the dashboard
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.card-item').forEach(function (item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? '' : 'none';
            });
            document.querySelectorAll('.deck-card').forEach(function (deck) {
                const text = deck.textContent.toLowerCase();
                deck.closest('.deck-col').style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // Filter cards by deck
    const deckFilter = document.getElementById('deckFilter');
    if (deckFilter) {
        deckFilter.addEventListener('change', function () {
            const deckId = this.value;
            document.querySelectorAll('.card-item').forEach(function (item) {
                item.style.display = (deckId === '' || item.dataset.deckId === deckId) ? '' : 'none';
            });
        });
    }

    // Reveal / hide the answer on a card
    document.querySelectorAll('.reveal-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = btn.closest('.card-item');
            card.classList.toggle('show-answer');
            btn.textContent = card.classList.contains('show-answer') ? 'Hide Answer' : 'Show Answer';
        });
    });

    // Confirm before deleting
    document.querySelectorAll('.delete-confirm').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });

    // Study mode: flip the flashcard
    const flashcard = document.getElementById('flashcard');
    if (flashcard) {
        flashcard.addEventListener('click', function () {
            flashcard.classList.toggle('flipped');
        });
    }

    // Study mode: next / previous / shuffle navigation
    const studyData = window.studyCards || [];
    let studyIndex = 0;
    let studyOrder = studyData.map(function (_, i) { return i; });

    const questionEl = document.getElementById('studyQuestion');
    const answerEl = document.getElementById('studyAnswer');
    const counterEl = document.getElementById('studyCounter');
    const progressEl = document.getElementById('studyProgress');

    function renderStudyCard() {
        if (!questionEl || studyOrder.length === 0) return;
        const card = studyData[studyOrder[studyIndex]];
        flashcard.classList.remove('flipped');
        setTimeout(function () {
            questionEl.textContent = card.question;
            answerEl.textContent = card.answer;
        }, 150);
        counterEl.textContent = 'Card ' + (studyIndex + 1) + ' of ' + studyOrder.length;
        progressEl.style.width = ((studyIndex + 1) / studyOrder.length * 100) + '%';
        progressEl.textContent = Math.round((studyIndex + 1) / studyOrder.length * 100) + '%';
    }

    const nextBtn = document.getElementById('nextCardBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            studyIndex = (studyIndex + 1) % studyOrder.length;
            renderStudyCard();
        });
    }

    const prevBtn = document.getElementById('prevCardBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            studyIndex = (studyIndex - 1 + studyOrder.length) % studyOrder.length;
            renderStudyCard();
        });
    }

    const shuffleBtn = document.getElementById('shuffleBtn');
    if (shuffleBtn) {
        shuffleBtn.addEventListener('click', function () {
            for (let i = studyOrder.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                const tmp = studyOrder[i];
                studyOrder[i] = studyOrder[j];
                studyOrder[j] = tmp;
            }
            studyIndex = 0;
            renderStudyCard();
        });
    }

    // Mark current study card as mastered
    const masteredBtn = document.getElementById('markMasteredBtn');
    if (masteredBtn) {
        masteredBtn.addEventListener('click', function () {
            const card = studyData[studyOrder[studyIndex]];
            fetch('toggle_mastered.php?id=' + card.id)
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        card.mastered = data.mastered;
                        masteredBtn.textContent = data.mastered ? 'Mastered ✓' : 'Mark as Mastered';
                        masteredBtn.classList.toggle('btn-success', !!data.mastered);
                        masteredBtn.classList.toggle('btn-outline-success', !data.mastered);
                    }
                });
        });
    }
});
