CREATE DATABASE savings_tracker;
USE savings_tracker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE savings_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    target_amount DECIMAL(10,2) NOT NULL,
    current_amount DECIMAL(10,2) DEFAULT 0,
    goal_name VARCHAR(100) NOT NULL,
    deadline DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Sample data
INSERT INTO users (username, password) VALUES 
('testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password is "password"

INSERT INTO savings_goals (user_id, target_amount, goal_name, deadline) VALUES
(1, 5000.00, 'New Laptop', '2024-12-31'),
(1, 10000.00, 'Emergency Fund', '2025-06-30');

INSERT INTO expenses (user_id, amount, category, date, description) VALUES
(1, 45.50, 'Food', '2024-01-15', 'Groceries'),
(1, 12.80, 'Transport', '2024-01-16', 'Bus fare');