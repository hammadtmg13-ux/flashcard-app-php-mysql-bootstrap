-- FlashLearn - Simple Flashcard Study App
-- Import with: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS flashlearn;
USE flashlearn;

CREATE TABLE IF NOT EXISTS decks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT '',
    color VARCHAR(20) DEFAULT 'primary',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deck_id INT NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    mastered TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deck_id) REFERENCES decks(id) ON DELETE CASCADE
);

-- Sample decks
INSERT INTO decks (name, description, color) VALUES
('HTML Basics', 'Core HTML tags and page structure', 'primary'),
('JavaScript Essentials', 'JS fundamentals every developer needs', 'warning'),
('MySQL Fundamentals', 'Queries, tables and relationships', 'success');

-- Sample cards: HTML Basics
INSERT INTO cards (deck_id, question, answer, mastered) VALUES
(1, 'What does HTML stand for?', 'HyperText Markup Language', 1),
(1, 'Which tag creates the largest heading?', 'The <h1> tag creates the largest heading.', 1),
(1, 'Which tag is used to create a link?', 'The <a> tag with the href attribute creates a link.', 0),
(1, 'What is the purpose of the <div> tag?', 'The <div> tag is a block-level container used to group other HTML elements.', 0),
(1, 'Which tag inserts an image?', 'The <img> tag with the src and alt attributes inserts an image.', 0);

-- Sample cards: JavaScript Essentials
INSERT INTO cards (deck_id, question, answer, mastered) VALUES
(2, 'How do you declare a variable that cannot be reassigned?', 'Use const. For example: const name = "Hammad";', 1),
(2, 'What does === check in JavaScript?', 'It checks both value and type, with no type conversion.', 0),
(2, 'Which method adds an item to the end of an array?', 'The push() method adds an item to the end of an array.', 0),
(2, 'What is an arrow function?', 'A shorter way to write a function, for example: const add = (a, b) => a + b;', 0),
(2, 'How do you select an element by its id?', 'Use document.getElementById("id") or document.querySelector("#id").', 1);

-- Sample cards: MySQL Fundamentals
INSERT INTO cards (deck_id, question, answer, mastered) VALUES
(3, 'Which statement retrieves all rows from a table?', 'SELECT * FROM table_name;', 0),
(3, 'What does the WHERE clause do?', 'It filters rows so only rows that match a condition are returned.', 1),
(3, 'What is a primary key?', 'A column (or set of columns) that uniquely identifies each row in a table.', 0),
(3, 'Which statement adds a new row to a table?', 'INSERT INTO table_name (columns) VALUES (values);', 0),
(3, 'What does JOIN do in SQL?', 'JOIN combines rows from two or more tables based on a related column.', 0);
