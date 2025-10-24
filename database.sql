-- Expense Manager Database Structure
-- Created for college-level PHP project

-- Create database
CREATE DATABASE IF NOT EXISTS expense_manager;
USE expense_manager;

-- Accounts table for authentication
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table for expense/income categorization
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES accounts(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, type, user_id) VALUES
('Salary', 'income', NULL),
('Freelance', 'income', NULL),
('Investment', 'income', NULL),
('Food', 'expense', NULL),
('Transportation', 'expense', NULL),
('Bills', 'expense', NULL),
('Entertainment', 'expense', NULL),
('Healthcare', 'expense', NULL),
('Shopping', 'expense', NULL),
('Education', 'expense', NULL);

-- Transactions table for storing all financial records
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- Create indexes for better performance
CREATE INDEX idx_user_transactions ON transactions(user_id);
CREATE INDEX idx_transaction_date ON transactions(transaction_date);
CREATE INDEX idx_transaction_type ON transactions(type);
