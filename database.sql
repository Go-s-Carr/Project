CREATE DATABASE foodconnect;
USE foodconnect;

-- Utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    role ENUM('client','restaurant','admin') DEFAULT 'client'
);

-- Restaurants
CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    owner_id INT,
    lat DOUBLE,
    lng DOUBLE
);

-- Menu
CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT,
    name VARCHAR(100),
    price DECIMAL(8,2)
);

-- Commandes
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    restaurant_id INT,
    total DECIMAL(8,2),
    status ENUM('pending','preparing','delivering','completed') DEFAULT 'pending'
);

-- Détails commande
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    dish_id INT,
    quantity INT,
    price DECIMAL(8,2)
);

-- Admin (password = 123456)
INSERT INTO users(username,email,password,role)
VALUES(
'admin',
'admin@mail.com',
'$2y$10$wH9Qy1JQ5z9Q9Yz9GqJx3eUuF2YjZ9z2nY7G7rZ2m0n5y7x9yJ9yK',
'admin'
);