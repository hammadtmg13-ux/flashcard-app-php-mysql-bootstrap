# FlashLearn - Simple Flashcard Study App

A simple flashcard study app built with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP. Create decks for any subject, add question and answer flashcards, then study with a flip-card mode that tracks what you have mastered.

## Features

- Create decks with a name, description and colour
- Add, edit and delete flashcards in any deck
- Study mode with a 3D flip-card animation, next and previous navigation, and shuffle
- Mark cards as mastered and track your mastery percentage
- Search flashcards and decks, and filter cards by deck
- Deck progress bars showing mastered cards per deck
- Stats: total decks, total cards, mastered cards and mastery percentage
- 3 sample decks included (HTML Basics, JavaScript Essentials, MySQL Fundamentals) with 15 sample cards

## Setup

1. Import the database:
   ```bash
   mysql -u root -p < database.sql
   ```
2. Update the database credentials in `config.php` if needed.
3. Serve the project folder with PHP:
   ```bash
   php -S localhost:8000
   ```
4. Open `http://localhost:8000` in your browser.

## Tech Stack

- HTML, CSS, JavaScript
- Bootstrap 5
- PHP (PDO)
- MySQL

Built by Hammad Khan.
