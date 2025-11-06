-- Create database
CREATE DATABASE IF NOT EXISTS coffeehub;
USE coffeehub;

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,20),
    image VARCHAR(255)
);

-- Create reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    comment TEXT,
    rating INT
);

-- Insert sample products (seed data)
INSERT INTO products (name, description, price, image) VALUES
('Espresso', 'Strong and bold espresso shot', 150.5, 'images/espresso.jpg'),
('Cappuccino', 'Espresso with steamed milk foam', 180.4, 'images/cappuccino.jpg'),
('Latte', 'Espresso with steamed milk', 170.5, 'images/latte.jpg'),
('Americano', 'Espresso with hot water', 160.4, 'images/americano.jpg'),
('Mocha', 'Chocolate flavored latte', 200.3, 'images/mocha.jpg'),
('Macchiato', 'Espresso with a dash of milk foam', 220.4, 'images/macchiato.jpg'),
('Flat White', 'Espresso with microfoam milk', 250.4, 'images/flatwhite.jpg'),
('Cold Brew', 'Slow brewed cold coffee', 300.4, 'images/coldbrew.jpg'),
('Irish Coffee', 'Coffee with whiskey and cream', 335.4, 'images/irishcoffee.jpg'),
('Affogato', 'Espresso poured over ice cream', 400.5, 'images/affogato.jpg');

-- Insert sample reviews (seed data)
INSERT INTO reviews (name, comment, rating) VALUES
('Alice', 'Great coffee and fast delivery!', 5),
('Bob', 'Loved the espresso, very authentic.', 4),
('Charlie', 'Good variety and quality.', 5);